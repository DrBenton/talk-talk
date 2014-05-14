<?php

$app['crypto.password.hash'] = $app->protect(
    function ($rawPassword) {
        return password_hash($rawPassword, PASSWORD_BCRYPT);
    }
);

$app['crypto.password.verify'] = $app->protect(
    function ($rawPassword, $passwordHash) {
        return password_verify($rawPassword, $passwordHash);
    }
);
