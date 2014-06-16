<?php

namespace TalkTalk\CorePlugin\Core\Service;

use League\Plates\Engine;
use League\Plates\Template;
use League\Plates\Extension\ExtensionInterface;
use TalkTalk\Core\Service\BaseService;
use TalkTalk\Core\ApplicationAwareInterface;

class View extends BaseService
{

    /**
     * @var \League\Plates\Engine
     */
    protected $platesEngine;
    /**
     * @var string
     */
    protected $templatesFilesExtension;

    protected $perfsTracking = false;

    public function render($templatePath, array $vars = array())
    {
        $startTime = microtime(true);
        $viewContent = $this->getRendering($templatePath, $vars);

        if ($this->perfsTracking) {
            $this->app->vars['perfs.view.rendering.duration'] = round((microtime(true) - $startTime) * 1000);
        }

        $this->app->vars['app.view.rendering_done'] = true;

        return $viewContent;
    }

    public function getRendering($templatePath, array $vars = array())
    {
        $this->initEngine();

        $template = new Template($this->platesEngine);

        return $template->render($templatePath, $vars);
    }

    public function setTemplatesFilesExtension($ext)
    {
        $this->templatesFilesExtension = $ext;
    }

    protected function initEngine()
    {
        if (null !== $this->platesEngine) {
            return;
        }

        $engine = new Engine();
        $engine->setFileExtension($this->templatesFilesExtension);

        $app = &$this->app;

        // Registered Extensions
        if (!empty($this->app->vars['view.extensions'])) {
            array_walk(
                $this->app->vars['view.extensions'],
                function ($extensionClass) use ($app, $engine) {
                    $extensionInstance = new $extensionClass;
                    if ($extensionInstance instanceof ApplicationAwareInterface) {
                        $extensionInstance->setApplication($app);
                    }
                    $engine->loadExtension($extensionInstance);
                }
            );
        }

        // Views folders init
        if (!empty($this->app->vars['view.folders'])) {
            array_walk(
                $this->app->vars['view.folders'],
                function ($viewFolderData) use ($engine) {
                    $engine->addFolder($viewFolderData['namespace'], $viewFolderData['path']);
                }
            );
        }

        // Do we tracks app performance?
        if ($this->app->vars['config']['debug']['perfs.tracking.enabled']) {
            $this->perfsTracking = true;
        }

        $this->platesEngine = $engine;
    }

}
