<?php

namespace TalkTalk\CorePlugin\Hooks\Plates\Extension;

use TalkTalk\CorePlugin\Core\Plates\Extension\BaseExtension;

class HtmlHooks extends BaseExtension
{

    public function getFunctions()
    {
        return array(
            'hooks' => 'getHooksObject'
        );
    }

    public function getHooksObject()
    {
        return $this;
    }

    public function html()
    {
        $hooksNames = func_get_args();
        call_user_func_array($this->app->getFunction('hooks.html.add'), $hooksNames);

        if ($this->app->vars['debug']) {
            return '<!-- HTML hook : "'.implode(', ', $hooksNames).'" --> ';
        } else {
            return '';
        }
    }

}
