<?php

$firewall = function () use ($app) {
    if (!$app->get('session')->has('phpbb.db-settings')) {
        throw new \DomainException('No phpBb settings defined!');
    }
};

return $firewall;