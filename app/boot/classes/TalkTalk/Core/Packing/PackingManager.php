<?php

namespace TalkTalk\Core\Packing;

use TalkTalk\Core\Application;
use TalkTalk\Core\ApplicationAwareInterface;

class PackingManager implements ApplicationAwareInterface
{

    const PACK_FILES_EXTENSION = '.pack.php';

    /**
     * @var \TalkTalk\Core\Application
     */
    protected $app;
    /**
     * @var string
     */
    protected $packsDir;

    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    public function setPacksDir($packsDir)
    {
        $this->packsDir = $packsDir;
    }

    /**
     * @param mixed $data
     * @param string $targetNamespace
     * @param string $targetId
     */
    public function packData($data, $targetNamespace, $targetId)
    {
        $dataAsPhp = var_export($data, true);
        $dataPackContent = "return $dataAsPhp;";
        $this->packPhpCode($dataPackContent, $targetNamespace, $targetId);
    }

    /**
     * @param string $rawPhpCode
     * @param string $targetNamespace
     * @param string $targetId
     */
    public function packPhpCode($rawPhpCode, $targetNamespace, $targetId)
    {
        $rawPhpCode = <<<END
<?php
if (!class_exists('TalkTalk\Core\Packing\PackingManager', false)) {
    die('Unauthorized access');
}

$rawPhpCode ;
END;
        $targetFilePath = $this->getPackDataFilePath($targetNamespace, $targetId);
        $targetDir = dirname($targetFilePath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        file_put_contents($targetFilePath, $rawPhpCode);
    }

    /**
     * @param string $targetNamespace
     * @param string $targetId
     * @return mixed
     */
    public function unpackData($targetNamespace, $targetId)
    {
        $packedDataFilePath = $this->getPackDataFilePath($targetNamespace, $targetId);
        return include $packedDataFilePath;
    }

    public function getPackDataFilePath($targetNamespace, $targetId)
    {
        if (false !== strpos($targetNamespace, '.') || false !== strpos($targetId, '.')) {
            throw new \RuntimeException('Dots are not allowed in data to pack namespace/id!');
        }

        return $this->packsDir . '/' . $targetNamespace . '/' . $targetId . self::PACK_FILES_EXTENSION;
    }
}