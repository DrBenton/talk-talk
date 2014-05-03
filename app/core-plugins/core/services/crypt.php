<?php

$app['crypt.password.hash'] = $app->protect(
    function ($rawPassword) {
        return password_hash($rawPassword, PASSWORD_BCRYPT);
    }
);

$app['crypt.password.verify'] = $app->protect(
    function ($rawPassword, $passwordHash) {
        return password_verify($rawPassword, $passwordHash);
    }
);
