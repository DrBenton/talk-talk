<?php

namespace TalkTalk\CorePlugin\Core\Service;

use League\Plates\Engine;
use League\Plates\Template;
use League\Plates\Extension\ExtensionInterface;
use TalkTalk\Core\Service\BaseService;

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
    /**
     * @var array
     */
    protected $templatesExtensions = array();

    public function render($templatePath, array $vars = array())
    {
        $this->initEngine();

        $template = new Template($this->platesEngine);

        echo $template->render($templatePath, $vars);
    }

    public function setTemplatesFilesExtension($ext)
    {
        $this->templatesFilesExtension = $ext;
    }

    public function addExtension(ExtensionInterface $extension)
    {
        $this->templatesExtensions[] = $extension;

        if (null !== $this->platesEngine) {
            $this->platesEngine->loadExtension($extension);
        }
    }

    protected function initEngine()
    {
        if (null !== $this->platesEngine) {
            return;
        }

        $engine = new Engine();
        $engine->setFileExtension($this->templatesFilesExtension);

        // Registered Extensions
        array_walk(
            $this->templatesExtensions,
            function (ExtensionInterface $extension) use ($engine) {
                $engine->loadExtension($extension);
            }
        );

        // Views folders init
        if (isset($this->app->vars['view.folders'])) {
            array_walk(
                $this->app->vars['view.folders'],
                function ($viewFolderData) use ($engine) {
                    $engine->addFolder($viewFolderData['namespace'], $viewFolderData['path']);
                }
            );
        }

        $this->platesEngine = $engine;
    }

}