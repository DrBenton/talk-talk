<?php

namespace TalkTalk\CorePlugins\Core\Services;

use TalkTalk\Core\Services\ServiceBase;

class FlashUtils extends ServiceBase
{
    /**
     * @inheritdoc
     */
    public static function getServiceName()
    {
        return 'flashUtils';
    }

    public function flashTranslated($key, $translationKey, $translationParams)
    {
        $this->app->flash(
            $key,
            $this->app->translator()->trans($translationKey, $translationParams)
        );
    }

    public function getFlashesByTypePrefix($keyPrefix)
    {
        return array_filter(
            $this->app->environment['slim.flash']->getMessages(),
            function ($flashMsgValue, $flashMsgKey) use ($keyPrefix) {
                return 0 === strpos($flashMsgKey, $keyPrefix);
            }
        );
    }

}