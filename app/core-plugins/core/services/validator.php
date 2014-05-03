<?php

use Illuminate\Validation\Validator;
use Illuminate\Validation\DatabasePresenceVerifier;

$app['validator.get'] = $app->protect(
    function($data, $rules, $messages = array(), $customAttributes = array()) use ($app) {
        $validator =  new Validator($app['translator'], $data, $rules, $messages, $customAttributes);
        $validator->setPresenceVerifier(new DatabasePresenceVerifier($app['db.connection_resolver']));
        
        return $validator;
    }
);

$app['validator.flash_validator_messages'] = $app->protect(
    function (Validator $validator) use ($app) {
        foreach ($validator->messages()->getMessages() as $field => $errors) {
            $errorData = array(
                'field' => $field,
                'messages' => $errors,
            );
            $app['session.flash.add']($errorData, 'error');
        }
    }
);
