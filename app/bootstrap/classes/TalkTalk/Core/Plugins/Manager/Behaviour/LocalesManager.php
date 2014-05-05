<?php

namespace TalkTalk\Core\Plugins\Manager\Behaviour;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Translation\Translator;

class LocalesManager extends BehaviourBase
{

    const CACHE_KEY = 'talk-talk/locales/locales-data';
    const CACHE_LIFETIME = 2000;

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $translator;

    public function __construct(Translator $appTranslator)
    {
        $this->translator = $appTranslator;
    }

    public function registerPluginsLocales()
    {
        if ($this->cache->contains(self::CACHE_KEY)) {
            // Let's restore all our plugins locales data from cache!
            $allPluginsLocalesData = $this->cache->fetch(self::CACHE_KEY);
            foreach ($allPluginsLocalesData as $pluginLocaleData) {
                $localeData = $pluginLocaleData['data'];
                $language = $pluginLocaleData['language'];
                $this->translator->addResource('array', $localeData, $language);
            }

            return;
        }

        $pluginsLocalesData = array();
        foreach ($this->pluginsManager->getPlugins() as $plugin) {
            if (!isset($plugin->data['locales'])) {
                continue;
            }

            foreach ($plugin->data['locales'] as $localeData) {

                $localeData = $this->getNormalizedLocaleData($localeData);

                $localeFilePath = $plugin->path . '/locales/' . $localeData['file'] . '.yml.php';
                $localeLanguage = $localeData['language'];

                $pluginsLocalesData[] = array(
                    'plugin_id' => $plugin->id,
                    'file_path' => $localeFilePath,
                    'language' => $localeLanguage,
                );
            }
        }

        foreach ($pluginsLocalesData as &$pluginLocaleData) {
            // We're gonna load all the locales right now, without lazy-loading.
            // This is more expensive at first run, but thanks to this
            // we will be able to cache the YAML files data.
            $localeData = Yaml::parse($pluginLocaleData['file_path']);

            $this->translator->addResource('array', $localeData, $pluginLocaleData['language']);

            $pluginLocaleData['data'] = $localeData;
        }

        // We won't do all these expensive operations again!
        $this->cache->save(self::CACHE_KEY, $pluginsLocalesData, self::CACHE_LIFETIME);
    }

    protected function getNormalizedLocaleData($localeData)
    {
        if (is_string($localeData)) {
            $localeData = array(
                'file' => $localeData,
                'language' => $localeData,
            );
        }

        return $localeData;
    }

}
