<?php

namespace TalkTalk\Core\Plugin\Config;

use TalkTalk\Core\Plugin\UnpackedPlugin;

class ClassesConfigPacker implements PluginConfigPackerInterface
{

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

        $classesToIncludeCode .= PHP_EOL . "/* end $nbClassesToInclude PHP Classes inclusions of plugin $plugin->id, path '$classesBasePath' */" . PHP_EOL ;

        return $classesToIncludeCode;
    }

}