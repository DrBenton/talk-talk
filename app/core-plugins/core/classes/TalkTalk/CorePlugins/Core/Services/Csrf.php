<?php

namespace TalkTalk\CorePlugins\Core\Services;

use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use TalkTalk\Core\Services\ServiceWithLazyPropsBase;

/**
 * Class Csrf
 * @package TalkTalk\CorePlugins\Core\Service
 * @property string $tokenName
 * @property string $tokenValue
 */
class Csrf extends ServiceWithLazyPropsBase
{
    /**
     * @inheritdoc
     */
    public static function getServiceName()
    {
        return 'csrf';
    }

    public function onActivation()
    {
        $app = $this->app;

        $this->singleton(
            'tokenName',
            function() use ($app) {
               return (string) $app->vars['config']['security']['csrf.token_name'];
            }
        );

        $this->singleton(
            'tokenValue',
            array($this, 'getTokenValue')
        );
    }

    protected function getTokenValue()
    {
        if ($_SESSION['csrf.token']) {
            return $_SESSION['csrf.token'];
        }

        // Token creation
        $tokenGenerator = new UriSafeTokenGenerator();
        $tokenValue = $tokenGenerator->generateToken();
        // The generated token is automatically copied to our Session data
        $_SESSION['csrf.token'] = $tokenValue;

        return $tokenValue;
    }

}