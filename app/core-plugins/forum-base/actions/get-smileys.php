<?php

use TalkTalk\Model\Smiley;

$action = function () use ($app) {
    $smileys = Smiley::byRank()->get();
    return $app->json(array(
      'smileysRootPath' => $app->get('settings')->get('app.smileys.location'),
      'smileys' => $smileys->toArray(),
    ));
};

return $action;
