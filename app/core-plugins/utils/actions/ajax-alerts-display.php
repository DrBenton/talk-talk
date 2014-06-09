<?php

$action = function () use ($app) {

    $receivedAlertsData = $app->getRequest()->request->get('alerts', null);

    if (null === $receivedAlertsData) {
        throw new \DomainException('Missing mandatory POST param "alerts"!');
    }

    $alertsToDisplay = array();
    foreach ($receivedAlertsData as $i => $alertData) {
        if (!isset($alertData['vars'])) {
            $alertData['vars'] = array();
        };
        $alertMsg = $app->get('translator')->trans($alertData['transKey'], $alertData['vars']);
        // "alerts-display.tpl.php" needs alerts grouped by type:
        $alertsToDisplay["alerts.$alertData[type].alert-$i"] = $alertMsg;
    }

    return $app->get('view')->render(
        'utils::ajax-alerts-display',
        array('alerts' => $alertsToDisplay)
    );
};

return $action;