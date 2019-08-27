<?php
// https://stackoverflow.com/a/25526473

namespace Univapay\Enums;

use OutOfRangeException;
use ReflectionClass;
use ReflectionMethod;

abstract class TypedEnum
{
    private static $instancedValues;

    private $value;
    private $name;

    private function __construct($value, $name)
    {
        $this->value = $value;
        $this->name = $name;
    }

    private static function fromGetter($getter, $value)
    {
        if ($value == null || $value == "") {
            return null;
        }

        $reflectionClass = new ReflectionClass(get_called_class());
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC);
        $className = get_called_class();

        foreach ($methods as $method) {
            if ($method->class === $className) {
                $enumItem = $method->invoke(null);

                if ($enumItem instanceof $className && $enumItem->$getter() === $value) {
                    return $enumItem;
                }
            }
        }

        throw new OutOfRangeException("$value is not defined in $className as an enum");
    }

    private static function getLastFunctionName()
    {
        $dbt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        return isset($dbt[2]['function']) ? strtolower($dbt[2]['function']) : null;
    }

    protected static function create($value = null)
    {
        if (self::$instancedValues === null) {
            self::$instancedValues = [];
        }

        $className = get_called_class();
        $value = isset($value) ? $value : self::getLastFunctionName();

        if (!isset(self::$instancedValues[$className])) {
            self::$instancedValues[$className] = [];
        }

        if (!isset(self::$instancedValues[$className][$value])) {
            $debugTrace = debug_backtrace();
            $lastCaller = array_shift($debugTrace);

            while ($lastCaller['class'] !== $className && count($debugTrace) > 0) {
                $lastCaller = array_shift($debugTrace);
            }

            self::$instancedValues[$className][$value] = new static($value, $lastCaller['function']);
        }

        return self::$instancedValues[$className][$value];
    }

    public static function findValues()
    {
        $values = [];
        $className = get_called_class();
        $reflectionClass = new ReflectionClass($className);
        $filter = ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC;
        
        foreach ($reflectionClass->getMethods($filter) as $method) {
            if (($method->getModifiers() & $filter) === $filter &&
            $method->class === $className &&
            strpos($method->name, 'of') !== 0
            ) {
                $values[] = $method->invoke(null);
            }
        }
        return $values;
    }

    public static function fromValue($value)
    {
        return self::fromGetter('getValue', $value);
    }

    public static function fromName($value)
    {
        return self::fromGetter('getName', $value);
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString()
    {
        return get_called_class() . "::{$this->name}";
    }
}
