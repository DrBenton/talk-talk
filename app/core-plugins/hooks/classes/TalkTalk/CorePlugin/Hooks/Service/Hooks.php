<?php

namespace TalkTalk\CorePlugin\Hooks\Service;

use TalkTalk\Core\ApplicationInterface;
use TalkTalk\Core\Service\BaseService;

class Hooks extends BaseService
{

    protected $pluginsHooksImplementations = array();

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        $this->app->vars['hooks.registry'] = array();
    }

    public function addHooksFile($hooksFilePath)
    {
        $hooks = array();
        $app = &$this->app;
        $myComponentsUrl = 'TODO';
        include_once $hooksFilePath;
        foreach ($hooks as $hookName => $hookImplementation) {
            $this->app->vars['hooks.registry'][$hookName][] = array(
                'priority' => 0,//TODO
                'implementation' => $hookImplementation,
            );
        }
    }

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

}
