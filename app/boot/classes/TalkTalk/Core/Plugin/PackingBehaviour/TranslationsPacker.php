<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\Plugin;
use Symfony\Component\Yaml\Yaml;

class TranslationsPacker extends BasePacker
{
    const TRANSLATION_FILE_PATH = '%plugin-path%/translations/%translation-name%.yml.php';

    protected $myConfigKey = '@translations';

    public function getPackerInitCode()
    {
        return <<<'PACKER_INIT_PHP_CODE'

namespace {
    $app->vars['translation.data'] = array();
}

PACKER_INIT_PHP_CODE;
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(Plugin $plugin)
    {
        if (empty($plugin->config[$this->myConfigKey])) {
            return null;
        }

        $myConfigPart = $plugin->config[$this->myConfigKey];

        $code = '';
        foreach ($myConfigPart as $translationName) {
            $code .= $this->getTranslationPhpCode($plugin, $translationName);
        }

        return $code;
    }

    protected function getTranslationPhpCode(Plugin $plugin, $translationName)
    {
        $translationFilePath = $this->replace(
            self::TRANSLATION_FILE_PATH,
            array(
                '%plugin-path%' => $plugin->path,
                '%translation-name%' => $translationName,
            )
        );

        $translationContent = file_get_contents($translationFilePath);
        $translationData = Yaml::parse($translationContent);

        if (!isset($translationData['@language'])) {
            throw new \DomainException(sprintf('Translations file "%s" must have a "@language" key!', $translationFilePath));
        }
        $translationLanguage = $translationData['@language'];
        unset($translationData['@language']);

        $translationFileInclusionCode = var_export($translationData, true);

        $pluginPhpCode = <<<'PLUGIN_PHP_CODE'

namespace {
    // Translation "%translation-name%" data (from Plugin "%plugin-id%"):
    // (will be used in "core-plugins/core/services/translator.php")
    $app->vars['translation.data']['%translation-language%'][] = %translation-file-inclusion-code%;
}

PLUGIN_PHP_CODE;

        // Job's done!
        return $this->replace(
            $pluginPhpCode,
            array(
                '%translation-file-inclusion-code%' => $translationFileInclusionCode,
                '%translation-language%' => $translationLanguage,
                '%translation-name%' => $translationName,
                '%plugin-id%' => $plugin->id,
            )
        );
    }
}
