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
        $phpBbDbConnection = $app['db.connection.factory']($dbSettings);
        $success = true;
    } catch (\PDOException $e) {
        $errMsg = array(
            'message' => $app['translator']->trans(
                'core-plugins.data-import-phpbb.start.db-error',
                array('%pdo_message%' => $app['html.escape']($e->getMessage()))
            ),
            'secured' => true
        );
        $app['session.flash.add']($errMsg, 'error');
        $success = false;
    }

    if (!$success) {
        return $app['twig']->render(
            'data-import-phpbb/start/start.twig',
            array('dbSettings' => $dbSettings)
        );
    } else {
        // Let's put these successfull connection setting in our Session data
        $app['session']->set('phpbb.db-settings', $dbSettings);
        // Let's display a successful notification
        $app['session.flash.add.translated'](
            'core-plugins.data-import-phpbb.start.db-success',
            array(),
            'success'
        );
        // And now, we just have to send the appropriate response!
        $targetUrl = $app['url_generator']->generate('data-import/phpbb/importing');
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
