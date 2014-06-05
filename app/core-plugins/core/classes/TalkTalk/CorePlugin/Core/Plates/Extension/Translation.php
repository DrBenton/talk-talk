<?php

namespace TalkTalk\CorePlugin\Core\Plates\Extension;

class Translation extends BaseExtension
{

    public function getFunctions()
    {
        return array(
            'trans' => 'trans'
        );
    }

    public function trans()
    {
        return call_user_func_array(
            array($this->app->get('translator'), 'trans'),
            func_get_args()
        );
    }

}
