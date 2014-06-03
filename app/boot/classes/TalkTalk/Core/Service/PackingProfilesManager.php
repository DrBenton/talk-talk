<?php

namespace TalkTalk\Core\Service;

use Symfony\Component\Yaml\Yaml;

class PackingProfilesManager extends BaseService
{

    const PACKS_METADATA_FILE_PATH = '%packs-dir%/packs-metadata.php';

    /**
     * @var string
     */
    protected $packsDir;
    /**
     * @var array
     */
    protected $packsMetadata;

    public function setPacksDir($packsDir)
    {
        $this->packsDir = $packsDir;
    }

    public function hasPackedProfileForClass($className)
    {
        $this->fetchPacksMetadata();

        if (isset($this->packsMetadata['handledPhpNamespaces'])) {

            foreach($this->packsMetadata['handledPhpNamespaces'] as $handledPhpNamespace => $packData) {
                if (0 === strpos($className, $handledPhpNamespace)) {
                    return true;
                }
            }

        }

        return false;
    }

    public function unpackProfileForClass($className)
    {
        $this->fetchPacksMetadata();

        if (isset($this->packsMetadata['handledPhpNamespaces'])) {

            foreach($this->packsMetadata['handledPhpNamespaces'] as $handledPhpNamespace => $packData) {
                if (0 === strpos($className, $handledPhpNamespace)) {
                    $this->getPackingManager()
                        ->unpackData($packData['namespace'], $packData['id']);

                    return;
                }
            }

        }

        throw new \DomainException(sprintf('No packed PHP profile found for class "%s"!', $className));
    }

    /**
     * @param string $profileYamlFilePath
     */
    public function packProfile($profileYamlFilePath)
    {
        $packProfileData = Yaml::parse($profileYamlFilePath);

        $baseDir = isset($packProfileData['packing']['base-dir'])
            ? $packProfileData['packing']['base-dir']
            : $this->app->vars['app.root_path'];
        $baseDir = $this->handlePathStr($baseDir);

        $packingManager = $this->getPackingManager();

        $that = &$this;

        $packedProfileCode = '';

        if (isset($packProfileData['files'])) {
            // Simple PHP files to pack (classes, ...)
            $filesToPack = array_map(
                function ($filePath) use (&$that, $baseDir) {
                    return $baseDir . '/' . $that->handlePathStr($filePath);
                },
                $packProfileData['files']
            );
            $packedProfileCode .= $packingManager->getPhpFilesCode(
                $filesToPack
            );
        }

        if (isset($packProfileData['filesIncludedByApp'])) {
            // PHP files which will be included via "Application#includeInApp"
            $filesIncludedByAppToPack = array_map(
                function ($filePath) use (&$that, $baseDir) {
                    return $baseDir . '/' . $that->handlePathStr($filePath);
                },
                $packProfileData['filesIncludedByApp']
            );
            $packedProfileCode .= $packingManager->getAppInclusionsCode(
                $filesIncludedByAppToPack
            );
        }

        // All right, let's pack all this PHP code into a file!
        $packingManager->packPhpCode(
            $packedProfileCode,
            $packProfileData['packing']['namespace'],
            $packProfileData['packing']['id']
        );

        $this->updatePacksMetadata($packProfileData);
    }

    public function clearAllPackedProfiles()
    {
        // Metadata clearing
        unlink($this->getPacksMetadataFilePath());
        $this->packsMetadata = null;

        // Packed profiles clearing
        $existingPackProfilesPaths = $this->app
            ->getService('utils.io')
            ->rglob('*.' . PackingManager::PACK_FILES_EXTENSION, $this->packsDir);
        array_walk($existingPackProfilesPaths, function ($path) {
            unlink($path);
        });
    }

    /**
     * This method is public only because we need to handle it from internal Closures.
     * @private
     */
    public function handlePathStr($path)
    {
        return str_replace(
            array('%app-root%', '%app-path%'),
            array($this->app->vars['app.root_path'], $this->app->vars['app.app_path']),
            $path
        );
    }

    /**
     * @return PackingManager
     */
    protected function getPackingManager()
    {
        return $this->app->getService('packing-manager');
    }

    protected function getPacksMetadataFilePath()
    {
        static $packsMetadataFilePath;

        if (null === $packsMetadataFilePath) {
            $packsMetadataFilePath = str_replace(
                array('%packs-dir%'),
                array($this->packsDir),
                self::PACKS_METADATA_FILE_PATH
            );
        }

        return $packsMetadataFilePath;
    }

    protected function fetchPacksMetadata($forceRefresh = false)
    {
        if (null !== $this->packsMetadata && !$forceRefresh) {
            return;
        }

        $packsMetadataFilePath = $this->getPacksMetadataFilePath();

        if (file_exists($packsMetadataFilePath)) {
            $packsMetadata = $this->app->includeInApp($packsMetadataFilePath);
        } else {
            $packsMetadata = array();
        }

        $this->packsMetadata = $packsMetadata;
    }

    protected function updatePacksMetadata(array $packedProfileData)
    {
        $this->fetchPacksMetadata();

        $packedNamespace = $packedProfileData['packing']['namespace'];
        $packedId = $packedProfileData['packing']['id'];

        if (isset($packedProfileData['packing']['handledPhPNamespace'])) {
            $handledPhpNamespace = (string) $packedProfileData['packing']['handledPhPNamespace'];
            $this->packsMetadata['handledPhpNamespaces'][$handledPhpNamespace] = array(
              'namespace' => $packedNamespace,
              'id' => $packedId,
            );
        }

        $now = date('Y-m-d H:i:s');

        $this->packsMetadata['generation-date'] = $now;

        $this->packsMetadata['packs'][] = "$packedNamespace::$packedId";
        $this->packsMetadata['packs'] = array_unique($this->packsMetadata['packs']);

        $generator = __METHOD__;
        $packsMetadataAsPhp = var_export($this->packsMetadata, true);
        $packsMetadataFileContent = <<<PACKS_METADATA_FILE_CONTENT
<?php
/* Generated by $generator at $now */

if (!defined('APP_ENVIRONMENT')) {
    die('Unauthorized access');
}

return $packsMetadataAsPhp;
PACKS_METADATA_FILE_CONTENT;

        file_put_contents($this->getPacksMetadataFilePath(), $packsMetadataFileContent);
    }

}