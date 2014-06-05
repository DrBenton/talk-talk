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

        // Hooks are sorted by priority
        usort($this->app->vars['hooks.registry'][$hookName], array($this, 'sortHooks'));

        // Go!
        $results = array();
        foreach($this->app->vars['hooks.registry'][$hookName] as $hookData) {
            $results[] = call_user_func($hookData['implementation'], $hookArgs);
        }

        return $results;
    }

    protected function sortHooks(array $hookA, array $hookB)
    {
        $priorityA = $hookA['priority'];
        $priorityB = $hookB['priority'];
        if ($priorityA > $priorityB) {
            return -1;
        } elseif ($priorityA < $priorityB) {
            return 1;
        } else {
            return 0;
        }
    }

}