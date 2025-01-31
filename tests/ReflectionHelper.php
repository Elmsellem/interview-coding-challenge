<?php

namespace Elmsellem\Tests;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class ReflectionHelper
{
    /**
     * @throws ReflectionException
     */
    public static function setProtectedProperty($object, string $property, $value): void
    {
        $reflection = new ReflectionClass($object);
        $prop = $reflection->getProperty($property);
        $prop->setValue($object, $value);
    }

    /**
     * @throws ReflectionException
     */
    public static function getProtectedProperty($object, string $property): mixed
    {
        $reflection = new ReflectionClass($object);
        $prop = $reflection->getProperty($property);

        return $prop->getValue($object);
    }

    /**
     * @throws ReflectionException
     */
    public static function invokeProtectedMethod($object, string $method, array $args = []): mixed
    {
        $method = new ReflectionMethod($object, $method);

        return $method->invoke($object, ...$args);
    }
}