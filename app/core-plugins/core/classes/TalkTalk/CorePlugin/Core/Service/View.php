<?php

namespace TalkTalk\CorePlugin\Core\Service;

use League\Plates\Engine;
use League\Plates\Template;
use TalkTalk\Core\Service\BaseService;

class View extends BaseService
{

    /**
     * @var \League\Plates\Engine
     */
    protected $platesEngine;
    protected $templatesFilesExtension;

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

    protected function initEngine()
    {
        if (null !== $this->platesEngine) {
            return;
        }

        $engine = new Engine();
        $engine->setFileExtension($this->templatesFilesExtension);

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