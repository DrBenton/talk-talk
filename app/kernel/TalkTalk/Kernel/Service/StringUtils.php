<?php

namespace TalkTalk\Kernel\Service;

class StringUtils extends BaseService
{

    public function escape($string)
    {
        return htmlentities($string, ENT_QUOTES);
    }

    public function appPathToUrl($appPath)
    {
        return $this->app->vars['app.root_url'] . '/' . $this->app->appPath($appPath);
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
