<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$action = function (Application $app, Request $request) {

    $dbSettings = $request->request->get('db-settings');
    if (null === $dbSettings || !is_array($dbSettings)) {
        throw new \RuntimeException();
    }

    // Let's check these DB connection settings!
    try {
        $app['phpbb.db.init']($dbSettings);
        $phpBbDbConnection = $app['db']->getConnection($app['phpbb.db.connection.name']);
        $success = true;
    } catch (\PDOException $e) {
        $errMsg = array(
            'message' => $app['translator']->trans(
                    'core-plugins.phpbb.start.db-error',
                    array('%pdo_message%' => $app['html.escape']($e->getMessage()))
                ),
            'secured' => true
        );
        $app['session.flash.add']($errMsg, 'error');
        $success = false;
    }

    if (!$success) {
        return $app['twig']->render(
            'phpbb/start/start.twig',
            array('dbSettings' => $dbSettings)
        );
    } else {
        // Let's put these successful connection setting in our Session data
        $app['session']->set('phpbb.db-settings', $dbSettings);
        // Let's display a "success" notification
        $app['session.flash.add.translated'](
            'core-plugins.import.phpbb.start.db-success',
            array(),
            'success'
        );
        // And now, we just have to send the appropriate response!
        $targetUrl = $app['url_generator']->generate('phpbb/import/importing');
        if ($app['isAjax']) {
            // JS response
            return $app['twig']->render(
                'utils/common/simple-redirect.ajax.twig',
                array('targetUrl' => $targetUrl)
            );
        } else {
            // Redirection to the next page, with flashed notification
            return $app->redirect($targetUrl);
        }
    }
};

return $action;
