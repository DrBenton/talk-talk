<?php

namespace TalkTalk\CorePlugin\Core\Service;

use TalkTalk\Core\Service\BaseService;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;

class Csrf extends BaseService
{

    public function getTokenName()
    {
        return (string) $this->app->vars['config']['security']['csrf.token_name'];
    }

    public function getTokenValue()
    {
        $tokenName = $this->getTokenName();

        if ($this->app->get('session')->has($tokenName)) {
            return $this->app->get('session')->get($tokenName);
        }

        // Token creation
        $tokenGenerator = new UriSafeTokenGenerator();
        $token = $tokenGenerator->generateToken();
        // The generated token is automatically copied to our Session data
        $this->app->get('session')->set($tokenName, $token);

        return $token;
    }

}
