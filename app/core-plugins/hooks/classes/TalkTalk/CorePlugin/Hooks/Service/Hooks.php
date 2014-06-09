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

        $hookRegisteredCallbacks = &$this->app->vars['hooks.registry'][$hookName];

        // Hooks are sorted by priority
        $this->app->get('utils.array')->sortBy($hookRegisteredCallbacks, 'priority');

        // Go!
        $results = array();
        foreach ($hookRegisteredCallbacks as $hookData) {
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
