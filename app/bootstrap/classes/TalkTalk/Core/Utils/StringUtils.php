<?php

namespace TalkTalk\Core\Utils;

class StringUtils
{

    /**
     * Returns 'true' if the given data looks like a JSON-encoded data string.
     *
     * @param  mixed $data
     * @return bool
     */
    public static function isJsonish($data)
    {
        $openingDelimiters = array('{', '[');
        $closingDelimiters = array('}', ']');

        return (
            is_string($data) &&
            in_array($data[0], $openingDelimiters) &&
            in_array($data[strlen($data)-1], $closingDelimiters)
        );
    }

}
