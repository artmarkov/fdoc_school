<?php

namespace main\util;

class DotArray
{

    public static function encode($data, $prefix = '')
    {
        $result = [];
        self::parseArray($data, $result, $prefix);
        return $result;
    }

    public static function decode($data, $prefix = '', $merge = false)
    {
        $result = [];
        foreach ($data as $k => $v) {
            if ($prefix && $prefix . '.' != substr($k, 0, strlen($prefix) + 1)) {
                continue;
            }
            $keys = explode('.', ($prefix ? substr($k, strlen($prefix) + 1) : $k));
            $link =& $result;
            if ($merge) {
                $link[$prefix ? substr($k, strlen($prefix) + 1) : $k] = $v;
            }
            foreach ($keys as $k) {
                if (!array_key_exists($k, $link)) {
                    $link[$k] = [];
                }
                $link =& $link[$k];
            }
            $link = $v;
        }
        return $result;
    }

    protected static function parseArray($data, &$result, $mainPrefix, &$prefix = null)
    {
        foreach ($data as $k => $v) {
            $key = ($mainPrefix ? $mainPrefix . '.' : '') . ($prefix ? $prefix . '.' . $k : $k);
            if (is_array($v)) {
                self::parseArray($v, $result, $key);
            } else {
                $result[$key] = $v;
            }
        }
    }
}