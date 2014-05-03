<?php

$app['auth.helpers.sign-up.get-form-validator'] = $app->protect(
    function (array $userData) use ($app) {
        $validator = $app['validator.get'](
            $userData,
            array(
                'login' => 'required|min:5|unique:users',
                'password' => 'required|confirmed|min:6',
                'email' => 'required|email|unique:users',
            )
        );

        return $validator;
    }
);