<?php

namespace TalkTalk\CorePlugins\Utils;

class ArrayUtils
{

    /**
     * Forces the "arrayness" of a $subject.
     * Only handles string -> array conversion at the moment... :-)
     *
     * <code>ArrayUtils::getArray('hello') => array('hello')</code>
     * <code>ArrayUtils::getArray('hello', 'key') => array('key' => 'hello')</code>
     * @param  mixed  $subject
     * @param  string $hashKeyName
     * @return array
     */
    public static function getArray($subject, $hashKeyName = null)
    {
        if (is_array($subject)) {
            return $subject;
        }

        if (is_string($subject)) {
            if (null === $hashKeyName) {
                $subject = array($subject);
            } else {
                $subject = array($hashKeyName => $subject);
            }
        }

        return $subject;
    }

}