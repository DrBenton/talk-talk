<?php

namespace TalkTalk\Core;

interface ApplicationInterface
{

    public function includeInApp($filePath);

    public function appPath($absoluteFilePath);

    public function checkAppPath($pathToCheck);

    public function defineService($serviceId, $serviceDefinition);

    public function getService($serviceId);

    public function addAction($urlPattern);

    public function run();

}