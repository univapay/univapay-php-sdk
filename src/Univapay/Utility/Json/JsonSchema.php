<?php

namespace Univapay\Utility\Json;

use Univapay\Utility\FunctionalUtils;
use ReflectionClass;
use Univapay\Utility\StringUtils;

class JsonSchema
{
    public $components;
    public $prefix;
    public $targetClass;

    public function __construct($targetClass, $prefix = null)
    {
        $this->components = [];
        $this->targetClass = $targetClass;
        $this->prefix = $prefix;
    }
    
    /**
     * @param Callable $formatter - Take in the following variables:
     *      $value - The value at the path
     *      $contextRoot - The root at the same level with $value
     *      $root - The original object that is being parsed
     *      $additionalArgs - An array that usually contains the context,
     *          but may be injected with additional args for object construction
     */
    public function with($path, $required = false, $formatter = 'Univapay\Utility\FunctionalUtils::identity')
    {
        array_push($this->components, new SchemaComponent($this->prefix . '/' .$path, $required, $formatter));
        return $this;
    }

    public function withNested(JsonSchema $schema)
    {
        foreach ($schema->components as $element) {
            array_push($this->components, $element);
        }
        return $this;
    }

    public function upsert($path, $required = false, $formatter = 'Univapay\Utility\FunctionalUtils::identity')
    {
        $index = FunctionalUtils::arrayFindIndex($this->components, function ($value) use ($path) {
            return $value->path === $path;
        });
        if ($index !== null) {
            $this->components = array_replace(
                $this->components,
                [$index => new SchemaComponent($this->prefix . '/' .$path, $required, $formatter)]
            );
            return $this;
        } else {
            return $this->with($path, $required = $required, $formatter = $formatter);
        }
    }

    private function getValues($json, array $parent = null, array $additionalArgs = [])
    {
        return array_map(function ($component) use ($json, $parent, $additionalArgs) {
            $path_parts = explode('/', $component->path);
            $value = JsonSchema::getField($json, $component->required, $path_parts);
            if ($value === null) {
                if ($component->required) {
                    throw new RequiredValueNotFoundException($component->path);
                } else {
                    return null;
                }
            }
            return call_user_func($component->formatter, $value, $json, $parent, $additionalArgs);
        }, $this->components);
    }

    public function parse($json, array $additionalArgs = [], array $parent = null)
    {
        $targetClass = new ReflectionClass($this->targetClass);
        $arguments = array_merge($this->getValues($json, $parent, $additionalArgs), $additionalArgs);
        return $targetClass->newInstanceArgs($arguments);
    }

    public function getParser(array $context = [])
    {
        return function ($json, $contextRoot = null, array $root = null, array $additionalArgs = []) use ($context) {
            $additionalArgs = empty($context) ? $additionalArgs : $context;
            return $this->parse($json, $additionalArgs, isset($root) ? $root : $contextRoot);
        };
    }

    public static function fromClass($targetClass, $snakeCase = true, $includeParentVars = true)
    {
        $classVars = FunctionalUtils::getClassVarsAssoc($targetClass, $includeParentVars);
        $newSchema = new JsonSchema($targetClass);
        return array_reduce(
            $classVars,
            function ($schema, $path) use ($snakeCase) {
                $realPath = $snakeCase ? StringUtils::toSnakeCase($path) : $path;
                return $schema->with($realPath);
            },
            $newSchema
        );
    }

    public static function getField($json, $required, array $paths)
    {
        if ($json === null && !$required) {
            return $json;
        }
        if (sizeof($paths) === 0) {
            throw new NoSuchPathException(null);
        }
        $nextKey = $paths[0];

        if (!array_key_exists($nextKey, $json)) {
            if ($required) {
                throw new NoSuchPathException($nextKey);
            } else {
                return null;
            }
        }
        $nextJson = $json[$nextKey];
        if (sizeof($paths) === 1) {
            return $nextJson;
        } else {
            try {
                return JsonSchema::getField($nextJson, $required, array_slice($paths, 1));
            } catch (NoSuchPathException $except) {
                throw new NoSuchPathException($nextKey . '/' . $except->path);
            }
        }
    }
}
