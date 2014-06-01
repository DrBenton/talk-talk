<?php

use TalkTalk\CorePlugins\Core\Services\Db as DbService;

// We have to create a new instance of our Service right now, because
// of the need to link immediately Illuminate components to the lazy-loaded DB connection.
$dbServiceInstance = new DbService();
$dbServiceInstance->initIlluminateEnvironment();

return $app->servicesManager->registerServiceClass(
  $dbServiceInstance
);

