<?php

$app->register(new Silex\Provider\SessionServiceProvider());

$app['session.flash.add'] = $app->protect(
    function ($message, $type = 'info') use ($app) {
        $app['session']->getFlashBag()->add($type, $message);
    }
);

$app['session.flash.add.translated'] = $app->protect(
    function ($translationKey, $translationParams, $type = 'info') use ($app) {
        $app['session.flash.add'](
            $app['translator']->trans(
                $translationKey,
                $translationParams
            )
            , $type);
    }
);

$app['session.flash.get'] = $app->protect(
    function ($type) use ($app) {
        return $app['session']->getFlashBag()->get($type, array());
    }
);

$app['session.flash.get.all'] = $app->protect(
    function () use ($app) {
        return $app['session']->getFlashBag()->all();
    }
);

$app['session.flash.clear'] = $app->protect(
    function () use ($app) {
        $app['session']->getFlashBag()->clear();
    }
);
