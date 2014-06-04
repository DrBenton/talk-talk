<?php

namespace TalkTalk\CorePlugin\Hooks\Service;

use TalkTalk\Core\Service\BaseService;

class Hooks extends BaseService
{
    
    protected $pluginsHooksImplementations = array();

    public function triggerPluginsHook($hookName, array $hookArgs = array())
    {
        if (empty($this->app->vars['hooks.registry'][$hookName])) {
            return array();
        }

        $results = array();
        foreach($this->app->vars['hooks.registry'][$hookName] as $hookImplementation) {
            $results[] = call_user_func($hookImplementation, $hookArgs);
        }

        return $results;
    }

}