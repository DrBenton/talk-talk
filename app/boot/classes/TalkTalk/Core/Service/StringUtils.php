<?php

namespace TalkTalk\Core\Service;

class StringUtils extends BaseService
{

    public function handlePluginRelatedString(\TalkTalk\Core\Plugin\UnpackedPlugin $plugin, $string)
    {
        $app = &$this->app;
        return str_replace(
            array('%plugin-path%', '%plugin-url%', '%vendors-url%'),
            array(
                $plugin->path,
                str_replace($app->vars['app.app_path'], $app->vars['app.base_url'], $plugin->path),
                str_replace($app->vars['app.app_path'], $app->vars['app.base_url'], $app->vars['app.js_vendors_path']),
            ),
            $string
        );
    }

}