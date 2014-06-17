<?php

namespace TalkTalk\CorePlugin\Core\Service;

use League\Plates\Engine;
use League\Plates\Template;
use League\Plates\Extension\ExtensionInterface;
use TalkTalk\Kernel\Service\BaseService;
use TalkTalk\Kernel\ApplicationAwareInterface;

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

        // Registered Extensions?
        $extensions = $this->app->get('hooks')->getHookFlattenedResult('view.get_extensions');
        if (!empty($extensions)) {
            array_walk(
                $extensions,
                function ($extension) use ($app, $engine) {
                    if ($extension instanceof ApplicationAwareInterface) {
                        $extension->setApplication($app);
                    }
                    $engine->loadExtension($extension);
                }
            );
        }

        // Templates folders init
        $templatesFolders = $this->app->get('hooks')->getHookFlattenedResult('view.get_templates_folders');
        if (!empty($templatesFolders)) {
            array_walk(
                $templatesFolders,
                function ($viewFolderData) use ($engine) {
                    $engine->addFolder($viewFolderData['namespace'], $viewFolderData['path']);
                }
            );
        }

        // Do we tracks app performance?
        if (!empty($this->app->vars['config']['debug']['perfs.tracking.enabled'])) {
            $this->perfsTracking = true;
        }

        $this->platesEngine = $engine;
    }

}
