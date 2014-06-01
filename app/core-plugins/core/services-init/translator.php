<?php

use TalkTalk\CorePlugins\Core\Services\TranslatorProxy;
use Symfony\Component\Translation\Translator;

$app->vars['locale_fallbacks'] = array('en');

return $app->servicesManager->registerServiceClass(
    'TalkTalk\CorePlugins\Core\Services\TranslatorProxy',
    function (TranslatorProxy $serviceInstance) use ($app) {
        $serviceInstance->setProxyTarget(new Translator());
        $serviceInstance->setFallbackLocales($app->vars['locale_fallbacks']);
    }
);

/*
// We shouldn't need the YamlFileLoader anymore...
// @see app/bootstrap/classes/TalkTalk/Core/Plugins/Manager/Behaviour/LocalesManager.php

use Symfony\Component\Translation\Loader\YamlFileLoader;
$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    return $translator;
}));
*/
