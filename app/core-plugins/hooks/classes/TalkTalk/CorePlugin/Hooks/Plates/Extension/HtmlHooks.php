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

    public function html($hookName)
    {
        $this->app->exec('hooks.html.add', $hookName);

        return "<!-- HTML hook : '$hookName' --> ";
    }

}
