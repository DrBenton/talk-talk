<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class ClassesPacker implements PluginPackerBehaviourInterface
{

    public function init(UnpackedPlugin $plugin)
    {
        // No specific initialization phase for this Packer
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        $myConfigPart = $plugin->config['@classes'];

        $code = '';
        foreach($myConfigPart as $classesData) {
            $code .= $this->getClassesPhpCode($plugin, $classesData);
        }

        return $code;
    }

    /**
     * @param \TalkTalk\Core\Plugin\UnpackedPlugin $plugin
     * @return array|null
     */
    public function getMetadata(UnpackedPlugin $plugin)
    {
        return null;
    }

    protected function getClassesPhpCode(UnpackedPlugin $plugin, array $classesData)
    {
        $classesBasePath = $plugin
            ->getAppService('utils.string')
            ->handlePluginRelatedString($plugin, $classesData['path']);

        $classesToIncludesPaths = $plugin
            ->getAppService('utils.io')
            ->rglob('*.php', $classesBasePath);

        $nbClassesToInclude = count($classesToIncludesPaths);
        $classesToIncludeCode = PHP_EOL . "/* begin $nbClassesToInclude PHP Classes inclusions of plugin $plugin->id, path '$classesBasePath' */" . PHP_EOL ;

        foreach($classesToIncludesPaths as $classFilePath) {

            // Class content formatting
            $classContent = $plugin
                ->getAppService('packing-manager')
                ->getPhpFileContentForPacking($classFilePath);

            // The formatted PHP Class content is appended to our packed Plugin PHP code
            $classesToIncludeCode .= $classContent;

        }

        // We also add the class resolution scheme to Composer,
        // so that we have these class available right now for the Plugins packing operation
        /*
        $plugin
            ->getAppService('autoloader')
            ->addPsr4(
                $classesData['prefix'],
                $classesBasePath
            );
        $classesToIncludeCode .= <<<PLUGIN_PHP_CODE
namespace {
    \$app->getService('autoloader')->addPsr4(
        '$classesData[prefix]\',
        '$classesBasePath'
    );
}
PLUGIN_PHP_CODE;
        */

        $classesToIncludeCode .= PHP_EOL . "/* end $nbClassesToInclude PHP Classes inclusions of plugin $plugin->id, path '$classesBasePath' */" . PHP_EOL ;

        return $classesToIncludeCode;
    }

}