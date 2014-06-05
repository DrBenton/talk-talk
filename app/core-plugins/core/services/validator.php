<?php

use Illuminate\Validation\Validator;
use Illuminate\Validation\DatabasePresenceVerifier;

$app->defineFunction(
    'validator.get',
    function ($data, $rules, $messages = array(), $customAttributes = array()) use ($app) {
        $validator = new Validator($app->get('translator'), $data, $rules, $messages, $customAttributes);
        $validator->setPresenceVerifier(new DatabasePresenceVerifier($app->get('db.connections.resolver')));

        return $validator;
    }
);

$app->defineFunction(
    'validator.flash_validator_messages',
    function (Validator $validator) use ($app) {
        foreach ($validator->messages()->getMessages() as $field => $errors) {
            $errorData = array(
                'field' => $field,
                'messages' => $errors,
            );
            $app->get('flash')->flashNow("alerts.error.$field", $errorData);
        }
    }
);