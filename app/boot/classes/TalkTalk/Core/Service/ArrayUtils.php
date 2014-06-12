<?php

namespace TalkTalk\Core\Service;

class ArrayUtils
{

    /**
     * Forces the "arrayness" of a $subject.
     * Only handles string -> array conversion at the moment... :-)
     *
     * <code>ArrayUtils::getArray('hello') => array('hello')</code>
     * <code>ArrayUtils::getArray('hello', 'key') => array('key' => 'hello')</code>
     *
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

    /**
     * Returns "true" if any of the array items is "true".
     *
     * @param  array $haystack
     * @return bool
     */
    public function containsTrue(array $haystack)
    {
        return in_array(true, $haystack, true);
    }

    /**
     * @param array  $array
     * @param string $fieldName
     */
    public function sortBy(array &$array, $fieldName)
    {
        $actionsSorter = function (array $actionA, array $actionB) use ($fieldName) {
            $priorityA = $actionA[$fieldName];
            $priorityB = $actionB[$fieldName];
            if ($priorityA > $priorityB) {
                return -1;
            } elseif ($priorityA < $priorityB) {
                return 1;
            } else {
                return 0;
            }
        };

        usort($array, $actionsSorter);
    }

}
