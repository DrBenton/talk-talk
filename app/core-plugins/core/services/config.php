<?php

/**
 * The "array_get" & "array_set" functions are copies of the great
 * Taylor Otwell's Laravel "array_set/array_get" function, adapted
 * for a "/" notation instead of "."
 *
 * License
 * The Laravel framework is open-sourced software licensed under the MIT license
 * @see http://opensource.org/licenses/MIT
 *
 */

/**
 * Set an array item to a given value using "/" notation.
 *
 * If no key is given to the method, the entire array will be replaced.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $value
 * @return array
 */
$array_set = function(&$array, $key, $value)
{
    if (is_null($key)) return $array = $value;

    $keys = explode('/', $key);

    while (count($keys) > 1)
    {
        $key = array_shift($keys);

        // If the key doesn't exist at this depth, we will just create an empty array
        // to hold the next value, allowing us to create the arrays to hold final
        // values at the correct depth. Then we'll keep digging into the array.
        if ( ! isset($array[$key]) or ! is_array($array[$key]))
        {
            $array[$key] = array();
        }

        $array =& $array[$key];
    }

    $array[array_shift($keys)] = $value;

    return $array;
};

/**
 * Get an item from an array using "/" notation.
 *
 * @param  array   $array
 * @param  string  $key
 * @param  mixed   $default
 * @return mixed
 */
$array_get = function ($array, $key, $default = null)
{
    if (is_null($key)) return $array;

    if (isset($array[$key])) return $array[$key];

    foreach (explode('/', $key) as $segment)
    {
        if ( ! is_array($array) or ! array_key_exists($segment, $array))
        {
            return value($default);
        }

        $array = $array[$segment];
    }

    return $array;
};

$app['config.set'] = $app->protect(
    function ($targetKeyWithDotNotation, $value) use ($app, &$array_set) {
        $appConfig = $app['config'];
        $array_set($appConfig, $targetKeyWithDotNotation, $value);
        $app['config'] = $appConfig;
    }
);
$app['config.get'] = $app->protect(
    function ($targetKeyWithDotNotation) use ($app, &$array_get) {
        return $array_get($app['config'], $targetKeyWithDotNotation);
    }
);