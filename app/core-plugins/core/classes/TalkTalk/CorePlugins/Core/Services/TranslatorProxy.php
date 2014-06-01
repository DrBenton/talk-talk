<?php

namespace TalkTalk\CorePlugins\Core\Services;

use Symfony\Component\Translation\Translator;
use TalkTalk\Core\Services\ServiceProxyBase;

/**
 * Class TranslatorProxy
 * Just a proxy class for Symfony Translator.
 * Most used functions are implemented, the others are handled with PHP magic methods.
 *
 * @package TalkTalk\Core\Services
 */
class TranslatorProxy extends ServiceProxyBase
{

    /**
     * @var \Symfony\Component\Translation\Translator
     */
    protected $proxyTarget;

    /**
     * @inheritdoc
     */
    public static function getServiceName()
    {
        return 'translator';
    }

    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->proxyTarget->trans($id, $parameters, $domain, $locale);
    }

    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        return $this->proxyTarget->transChoice($id, $number, $parameters, $domain, $locale);
    }

}