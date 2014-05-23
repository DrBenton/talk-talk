<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\InvalidParameterException;

$action = function (Application $app, Request $request) {

    $receivedAlertsData = $request->request->get('alerts', null);

    if (null === $receivedAlertsData) {
        throw new InvalidParameterException('Missing mandatory POST param "alerts"!');
    }

    $alertsToDisplay = array();
    foreach ($receivedAlertsData as $alertData) {
        if (!isset($alertData['vars'])) {
            $alertData['vars'] = array();
        };
        $alertMsg = $app['translator']->trans($alertData['transKey'], $alertData['vars']);
        // "alerts-display.twig" needs alerts grouped by type:
        $alertsToDisplay[$alertData['type']][] = $alertMsg;
    }

    return $app['twig']->render(
        'utils/ajax-alerts-display.twig',
        array('alerts' => $alertsToDisplay)
    );
};

return $action;
