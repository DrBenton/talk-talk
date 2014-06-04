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
                $app->vars['app.base_url'] . $plugin->path,
                $app->vars['app.base_url'] . $this->app->appPath($app->vars['app.js_vendors_path'])
            ),
            $string
        );
    }

}
