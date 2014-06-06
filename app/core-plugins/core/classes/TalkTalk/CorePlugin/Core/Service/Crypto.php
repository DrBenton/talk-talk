<?php

namespace TalkTalk\CorePlugin\Core\Service;

use TalkTalk\Core\Service\BaseService;
use TalkTalk\Core\ApplicationInterface;

class Crypto extends BaseService
{

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        if (!function_exists('password_hash')) {

            // Let's first try to use the library from a data pack
            $dataPackResult = $this->app
                ->get('packing-manager')
                ->unpackData('vendors', 'ircmaxell-passwordcompat');

            if (-1 === $dataPackResult) {
                // Hum, no data pack has been found for this library.
                // --> Well, let's wake up Composer!
                $this->app->get('autoloader');
            }

        }
    }

    public function hashPassword($rawPassword)
    {
        return password_hash($rawPassword, PASSWORD_BCRYPT);
    }

    public function verifyPassword($rawPassword, $passwordHash)
    {
        $res = password_verify($rawPassword, $passwordHash);
        //$this->app->get('logger')->debug("verifyPassword('$rawPassword', '$passwordHash') => ".((int) $res));
        return $res;
    }

}
