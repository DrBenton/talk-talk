<?php

namespace TalkTalk\Core\Plugin\PackingBehaviour;

use TalkTalk\Core\Plugin\UnpackedPlugin;
use Symfony\Component\Yaml\Yaml;

class TranslationsPacker extends BasePacker
{
    const TRANSLATION_FILE_PATH = '%plugin-path%/translations/%translation-name%.yml.php';

    protected $myConfigKey = '@translations';

    public function getPackerInitCode()
    {
        return <<<PLUGIN_PHP_CODE
namespace {
    \$app->vars['translation.data'] = array();
}

PLUGIN_PHP_CODE;
    }

    /**
     * @inheritdoc
     */
    public function getPhpCodeToPack(UnpackedPlugin $plugin)
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

    protected function getTranslationPhpCode(UnpackedPlugin $plugin, $translationName)
    {
        $translationFilePath = str_replace(
            array('%plugin-path%', '%translation-name%'),
            array($plugin->path, $translationName),
            self::TRANSLATION_FILE_PATH
        );

        $translationContent = file_get_contents($translationFilePath);
        $translationData = Yaml::parse($translationContent);

        if (!isset($translationData['@language'])) {
            throw new \DomainException(sprintf('Translations file "%s" must have a "@language" key!', $translationFilePath));
        }
        $translationLanguage = $translationData['@language'];
        unset($translationData['@language']);

        $translationFileInclusionCode = var_export($translationData, true);

        return <<<PLUGIN_PHP_CODE
namespace {
    // Translation "$translationName" data:
    \$app->vars['translation.data']['$translationLanguage'][] = $translationFileInclusionCode;
}

PLUGIN_PHP_CODE;
    }
}
