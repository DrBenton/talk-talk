<?php

namespace TalkTalk\Core\Service;

class StringUtils extends BaseService
{

    public function handlePluginRelatedString(\TalkTalk\Core\Plugin\Plugin $plugin, $string)
    {
        static $vendorsUrl;
        if (null === $vendorsUrl) {
            $vendorsUrl = $this->app->vars['app.base_url'] . '/' . $this->app->appPath($this->app->vars['app.js_vendors_path']);
        }

        return $this->replace(
            $string,
            array(
                '%plugin-path%' => $plugin->path,
                '%plugin-url%' => $this->app->vars['app.base_url'] . '/' . $plugin->path,
                '%vendors-url%' => $vendorsUrl,
            )
        );
    }

    public function indent($text, $nbIndents = 1, $indent = '    ')
    {
        $indentation = str_repeat($indent, $nbIndents);

        return $indentation . preg_replace('~^([^A-Z]+)~m', $indentation . '$1', $text);
    }

    /**
     * Returns 'true' if the given data looks like a JSON-encoded data string.
     *
     * @param  mixed $data
     * @return bool
     */
    public function isJsonish($data)
    {
        $openingDelimiters = array('{', '[');
        $closingDelimiters = array('}', ']');

        return (
            is_string($data) &&
            in_array($data[0], $openingDelimiters) &&
            in_array($data[strlen($data)-1], $closingDelimiters)
        );
    }

    /**
     * More convenient API for str_replace
     * @param $string
     * @param  array  $varsMap
     * @return string
     */
    public function replace($string, array $varsMap)
    {
        return str_replace(
            array_keys($varsMap),
            array_values($varsMap),
            $string
        );
    }

    /**
     * Converts underscored or dasherized string to a camelized one.
     * Begins with a lower case letter unless it starts with an underscore or string
     * @param  string $string
     * @return string
     */
    public function camelize($string)
    {
        return preg_replace_callback(
            '~[-_\s]+(.)~',
            function (array $matches) {
                return strtoupper($matches[1]);
            },
            $string
        );
    }

}
