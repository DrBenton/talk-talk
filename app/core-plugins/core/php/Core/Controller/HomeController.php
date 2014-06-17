<?php

namespace TalkTalk\CorePlugin\Core\Controller;

class HomeController extends BaseController
{

    public function home()
    {
        if ($this->isAjax) {
            return new JsonResponse(array('error' => array('msg' => 'Not available in Ajax mode')), 500);
        }

        return $this->app->get('view')->render('core::home');
    }

}