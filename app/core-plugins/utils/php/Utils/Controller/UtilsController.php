<?php

namespace TalkTalk\CorePlugin\Utils\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use TalkTalk\CorePlugin\Core\Controller\BaseController;

class UtilsController extends BaseController
{

    public function phpinfo()
    {
        if ($this->isAjax) {
            return new JsonResponse(array('error' => array('msg' => 'Not available in Ajax mode')), 500);
        }

        ob_start();
        phpinfo();

        return ob_get_clean();
    }

    public function compileJsApp()
    {
        return $this->app->get('view')->render('utils::app-js-compilation');
    }

    public function saveJsAppCompilation()
    {
        $jsContent = $this->app->getRequest()->request->get('jsContent');
        if (null === $jsContent) {
            throw new \Exception('No "jsContent" param provided!');
        }

        $targetFilePath = $this->app->vars['app.var_path'] . '/assets/app-core.js';
        $targetFileDir = dirname($targetFilePath);
        if (!is_dir($targetFileDir)) {
            mkdir($targetFileDir, 0777, true);
        }

        file_put_contents($targetFilePath, $jsContent);

        return $this->app->json(array('success' => true));
    }

}