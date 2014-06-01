<?php

namespace TalkTalk\CorePlugins\Core\Services;

use TalkTalk\Core\Services\ServiceBase;

class Crypto extends ServiceBase
{
    /**
     * @inheritdoc
     */
    public static function getServiceName()
    {
        return 'crypto';
    }

    public function getPasswordHash($rawPassword, $algo = PASSWORD_BCRYPT)
    {
        return password_hash($rawPassword, $algo);
    }

    public function verifyPassword($rawPassword, $passwordHash)
    {
        return password_verify($rawPassword, $passwordHash);
    }
}