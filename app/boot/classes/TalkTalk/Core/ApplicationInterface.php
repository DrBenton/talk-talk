<?php

namespace TalkTalk\Core;

interface ApplicationInterface
{

    const EARLY_EVENT = 512;
    const LATE_EVENT  = -512;

    public function includeInApp($filePath);

    public function appPath($absoluteFilePath);

    public function checkAppPath($pathToCheck);

    public function defineService($serviceId, $serviceDefinition);

    public function getService($serviceId);

    /**
     * An alias for "get()"
     * @param $serviceId
     * @return mixed
     */
    public function get($serviceId);

    public function defineFunction($functionId, $callable);

    public function execFunction($functionId);

    /**
     * An alias for "execFunction()"
     * @param $functionId
     * @return mixed
     */
    public function exec($functionId);

    public function getFunction($functionId);

    public function addAction($urlPattern);

    public function run();

    public function getResponse();

    public function before($callable, $priority = 0);

    public function after($callable, $priority = 0);

    public function error($callable, $priority = 0);

    public function path($actionName, $params = array());

    public function redirect($url, $status = 302);

    public function redirectToAction($actionName, $params = array(), $status = 302);

}
