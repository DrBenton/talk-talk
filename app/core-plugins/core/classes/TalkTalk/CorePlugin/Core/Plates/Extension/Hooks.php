<?php

namespace TalkTalk\CorePlugin\Core\Plates\Extension;

use League\Plates\Extension\ExtensionInterface;

class Hooks implements ExtensionInterface
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
        return "<!-- HTML hook : $hookName --> ";
    }

}
