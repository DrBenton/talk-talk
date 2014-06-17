<?php

namespace TalkTalk\Kernel\Service;

class Hooks extends BaseService
{

    protected $hooksRegistrations = array();

    /**
     * @param string $hookName
     * @param array $args
     * @return array
     */
    public function triggerHook($hookName, array $args = array())
    {
        if (empty($this->hooksRegistrations[$hookName])) {
            return array();
        }

        $hooksRegistrations = $this->hooksRegistrations[$hookName];
        $this->app->get('utils.array')->sortBy($hooksRegistrations, 'priority');

        $hookResults = array();
        foreach ($hooksRegistrations as $hookRegistration) {
            $hookResult = call_user_func_array($hookRegistration['callback'], $args);
            if (null !== $hookResult) {
                $hookResults[] = $hookResult;
            }
        }

        return $hookResults;
    }

    public function getHookFlattenedResult($hookName, array $args = array())
    {
        $hookResults = $this->triggerHook($hookName, $args);

        return $this->app->get('utils.array')->flattenArray($hookResults);
    }

    public function onHook($hookName, $callback, $priority = 0)
    {
        $this->hooksRegistrations[$hookName][] = array(
            'callback' => $callback,
            'priority' => $priority,
        );
    }

}
