<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class ClassesPacker extends BasePacker
{

    protected $myConfigKey = '@classes';

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
    {
        if (!isset($plugin->config[$this->myConfigKey])) {
            return null;
        }

        $myConfigPart = $plugin->config[$this->myConfigKey];

        $code = '';
        foreach($myConfigPart as $classesData) {
            $code .= $this->getClassesPhpCode($plugin, $classesData);
        }

        return $code;
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

        /*
        // We also add the class resolution scheme to Composer as a fallback, just in case...
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