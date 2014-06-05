<?php

use TalkTalk\CorePlugin\Core\Service\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

$app->vars['translation.default_language'] = 'en';

$app->defineService(
    'translator',
    function () use ($app) {
        $service = new Translator($app->vars['translation.default_language']);
        $service->addLoader('array', new ArrayLoader());

        foreach ($app->vars['translation.data'] as $language => $translationsData) {
            foreach ($translationsData as $translationData) {
                $service->addResource('array', $translationData, $language);
            }
        }

        return $service;
    }
);
