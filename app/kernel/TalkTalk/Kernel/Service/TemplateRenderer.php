<?php

namespace TalkTalk\Kernel\Service;

class TemplateRenderer extends BaseService
{
    public function renderTemplate($templatePath)
    {
        ob_start();
        $this->app->includeInApp($templatePath);

        return ob_get_clean();
    }
}