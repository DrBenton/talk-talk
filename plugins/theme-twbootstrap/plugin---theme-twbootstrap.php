<?php

namespace TalkTalk\Plugin\TWBootstrap;

/**
 * Actions
 */

// No Action for this Plugin


/**
 * Classes init
 */

// No Classes for this Plugin


/**
 * App Services & Functions definition
 */

// No Services or Functions for this Plugin


/**
 * Views extensions
 */

// No Views Extensions for this Plugin

/**
 * Hooks
 */
$app->get('hooks')->addHooksFile(__DIR__ . '/plugin-hooks.php');


/**
 * Plugin assets
 */
$assets['css'][] = array('url' => '%plugin-url%/assets/components/bootstrap/dist/css/bootstrap.css');
$assets['css'][] = array('url' => '%plugin-url%/assets/css/theme-twbootstrap.css');

