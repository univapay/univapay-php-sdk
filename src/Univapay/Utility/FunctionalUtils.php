<?php

namespace Univapay\Utility;

abstract class FunctionalUtils
{

    public static function getOrElse(array $array, $key, $orElse)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            return $orElse;
        }
    }

    public static function getOrNull($array, $key)
    {
        return FunctionalUtils::getOrElse($array, $key, null);
    }

    public static function copy(array $array)
    {
        return array_merge([], $array);
    }

    public static function identity($a)
    {
        return $a;
    }

    public static function arrayFindIndex($xs, $f)
    {
        $index = 0;
        foreach ($xs as $x) {
            if (call_user_func($f, $x) === true) {
                return $index;
            }
            $index++;
        }
        return null;
    }

    public static function getClassVarsAssoc($called, $includeParentVars)
    {
        $classVars = array_keys(get_class_vars($called));

        $parent = get_parent_class($called);
        while ($parent !== false) {
            $parentVars = array_keys(get_class_vars($parent));
            $classVars = array_diff($classVars, $parentVars);
            $classVars = $includeParentVars ? array_merge($parentVars, $classVars) : $classVars;
            $parent = get_parent_class($parent);
        }

        return $classVars;
    }

    public static function stripNulls(array $array)
    {
        return array_filter($array, function ($val) {
            return $val !== null;
        });
    }
}
