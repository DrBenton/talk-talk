<?php

use Symfony\Component\HttpFoundation\Request;
use TalkTalk\CorePlugins\PhpBb\Model\Forum as PhpBbForum;

$action = function (Request $request) use ($app) {

    $dbSettings = $request->request->get('db-settings');
    if (null === $dbSettings || !is_array($dbSettings)) {
        throw new \DomainException();
    }

    // Let's check these DB connection settings!
    try {
        $app->exec('phpbb.db.init', $dbSettings);
        $randomForum = PhpBbForum::query()->take(1)->get()->first();
        if (null !== $randomForum) {
            $success = true;
        }
    } catch (\PDOException $e) {
        $errMsg = array(
            'message' => $app->get('translator')->trans(
                'core-plugins.phpbb.import.start.db-error',
                array('%pdo_message%' => $app->exec('utils.html.escape', $e->getMessage()))
            ),
            'secured' => true
        );
        $app->get('flash')->flash('alerts.error.phpbb-db-settings', $errMsg);
        $success = false;
    }

    if (!$success) {
        return $app->get('view')->render(
            'phpbb::import/start',
            array('dbSettings' => $dbSettings)
        );
    } else {
        // Let's put these successful connection settings in our Session data
        $app->get('session')->set('phpbb.db-settings', $dbSettings);
        // Let's display a "success" notification
        $app->get('flash')->flashTranslated(
            'alerts.success.phpbb-db-settings',
            'core-plugins.phpbb.import.start.db-success',
            array()
        );
        // And now, we just have to send the appropriate response!
        $targetUrl = $app->path('phpbb/import/importing');
        if ($app->vars['isAjax']) {
            // JS response
            return $app->get('view')->render(
                'utils::common/simple-redirect.ajax',
                array('targetUrl' => $targetUrl)
            );
        } else {
            // Redirection to the next page, with flashed notification
            return $app->redirect($targetUrl);
        }
    }
};

return $action;
