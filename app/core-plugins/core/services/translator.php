<?php

use Symfony\Component\Translation\Loader\YamlFileLoader;

$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));

$app['translator.locales_path'] = $app['app.path'] . '/app/data/locales';

$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', $app['translator.locales_path'] . '/en.yml.php', 'en');

    return $translator;
}));
