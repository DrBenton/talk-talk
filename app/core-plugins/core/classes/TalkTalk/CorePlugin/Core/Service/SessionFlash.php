<?php

namespace TalkTalk\CorePlugin\Core\Service;

use TalkTalk\Core\Service\BaseService;

class SessionFlash extends BaseService
{

    public function __construct()
    {
        session_start();
    }

    public function flash($flashKey, $flashValue)
    {
        $this->app->getService('slim')->flash($flashKey, $flashValue);
    }

    public function flashTranslated($flashKey, $flashValueTranslationKey, $flashValueTranslationParams = array())
    {
        $this->flash(
            $flashKey,
            $this->app->getService('translator')->trans($flashValueTranslationKey, $flashValueTranslationParams)
        );
    }

}
