<?php

$app->defineService(
    'autoloader',
    function () use ($app) {
        $autoloader = include $app->vars['app.php_vendors_path'] . '/autoload.php';

        $autoloader->add('TalkTalk\Core\\', $app->vars['app.boot_path'] . 'classes');

        // As we have our own "packed PHP files" autoload system, we don't want Composer
        // to prepend its autoloader. Let's unregister & register it, without the "prepend" option!
        $autoloader->unregister();
        $autoloader->register(false);

        return $autoloader;
    }
);
