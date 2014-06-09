<?php

namespace TalkTalk\CorePlugin\Core\Plates\Extension;

class Translation extends BaseExtension
{

    /**
     * Instance of the current template.
     * @var Template
     */
    public $template;

    public function getFunctions()
    {
        return array(
            'trans' => 'trans',
            'transE' => 'transEscaped',
        );
    }

    /**
     * A simple call to our Symfony Translator
     * @return string
     */
    public function trans()
    {
        return call_user_func_array(
            array($this->app->get('translator'), 'trans'),
            func_get_args()
        );
    }

    /**
     * Returns an escaped translation
     * @return string
     */
    public function transEscaped()
    {
        return $this->template->e(
            call_user_func_array(
                array($this, 'trans'),
                func_get_args()
            )
        );
    }

}
