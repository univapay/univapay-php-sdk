<?php

namespace Univapay\Resources;

trait Jsonable
{
    protected static $schema;

    // Required to be implemented but causes an exception to be thrown in strict mode on PHP >5.3 && <7
    // protected abstract static function initSchema();

    public static function getSchema()
    {
        if (!isset(self::$schema)) {
            self::$schema = self::initSchema();
        }
        return self::$schema;
    }

    public static function getContextParser($context)
    {
        return self::getSchema()->getParser([$context]);
    }
}
