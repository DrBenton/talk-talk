<?php

namespace TalkTalk\CorePlugins\Core\Services;

use Symfony\Bridge\Twig\Extension\TranslationExtension;
use TalkTalk\Core\Services\ServiceProxyBase;
use TalkTalk\CorePlugins\Core\PluginsManagerBehaviour\TwigViewsFinder;
use TalkTalk\CorePlugins\Core\PluginsManagerBehaviour\AssetsManager;
use TalkTalk\CorePlugins\Core\PluginsManagerBehaviour\TwigExtensionsManager;

/**
 * Class TwigProxy
 * Just a proxy class for Twig Environment.
 * Most used functions are implemented, the others are handled with PHP magic methods.
 *
 * @package TalkTalk\Core\Services
 */
class TwigProxy extends ServiceProxyBase
{

    /**
     * @var \Twig_Environment
     */
    protected $proxyTarget;

    /**
     * @inheritdoc
     */
    public static function getServiceName()
    {
        return 'twig';
    }

    public function onActivation()
    {
        $pluginsManager = $this->app->pluginsManager;

        // Views paths init
        $pluginsManager->addBehaviour(new TwigViewsFinder());
        $viewsPaths = $pluginsManager->getPluginsViewsPaths();
        $twigLoader = new \Twig_Loader_Filesystem($viewsPaths);
        $this->proxyTarget->setLoader($twigLoader);

        // Plugins Twig Extensions registering
        $pluginsManager->addBehaviour(new TwigExtensionsManager());
        $pluginsManager->registerTwigExtensions();

        // Plugins assets init
        $pluginsManager->addBehaviour(new AssetsManager());
        $pluginsManager->registerPluginsAssets();


        if (isset($this->app->translator)) {
            $this->proxyTarget->addExtension(new TranslationExtension($this->app->translator));
        }
    }

    public function render($name, array $context = array())
    {
        return $this->proxyTarget->render($name, $context);
    }



}