<?php

declare(strict_types=1);

namespace Swis\JsonApi\Client;

/**
 * @internal
 */
class Util
{
    private static array $camelCache = [];

    private static array $snakeCache = [];

    private static array $studlyCache = [];

    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function stringLower(string $value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     *
     * @return string
     */
    public static function stringSnake(string $value, string $delimiter = '_')
    {
        $key = $value;

        if (isset(self::$snakeCache[$key][$delimiter])) {
            return self::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::stringLower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return self::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function stringStudly(string $value)
    {
        $key = $value;

        if (isset(self::$studlyCache[$key])) {
            return self::$studlyCache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return self::$studlyCache[$key] = str_replace(' ', '', $value);
    }

    /**
     * Convert a value to camel case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function stringCamel(string $value)
    {
        if (isset(self::$camelCache[$value])) {
            return self::$camelCache[$value];
        }

        return self::$camelCache[$value] = lcfirst(static::stringStudly($value));
    }

    /**
     * Remove array element from array.
     *
     * @param array $array
     * @param mixed $keys
     *
     * @return array
     */
    public static function arrayExcept(array $array, $keys)
    {
        if (is_string($keys)) {
            $keys = [$keys];
        }

        if (count($keys) === 0) {
            return $array;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
