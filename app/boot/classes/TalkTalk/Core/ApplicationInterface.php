<?php

namespace TalkTalk\Core;

interface ApplicationInterface
{

    public function includeInApp($filePath);

    public function appPath($absoluteFilePath);

    public function checkAppPath($pathToCheck);

    public function defineService($serviceId, $serviceDefinition);

    public function getService($serviceId);

    public function defineFunction($functionId, $callable);

    public function execFunction($functionId);

    public function getFunction($functionId);

    public function addAction($urlPattern);

    public function run();

    public function getResponse();

    public function before($callable, $priority = 0);

    public function after($callable, $priority = 0);

    public function path($actionName, $params = array());

    public function redirect($actionName, $params = array(), $status = 302);

}
