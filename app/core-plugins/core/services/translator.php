<?php

//use Symfony\Component\Translation\Loader\YamlFileLoader;

$app->register(
    new Silex\Provider\TranslationServiceProvider(),
    array(
        'locale_fallbacks' => array('en'),
    )
);

$app['translator.locales_path'] = $app['app.path'] . '/app/data/locales';

/*
// We shouldn't need the YamlFileLoader anymore...
// @see app/bootstrap/classes/TalkTalk/Core/Plugins/Manager/Behaviour/LocalesManager.php
$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    return $translator;
}));
*/
