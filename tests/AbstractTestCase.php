<?php

namespace Joesama\Webhook\Tests;

use Orchestra\Testbench\TestCase as Testbench;
use ReflectionClass;
use ReflectionProperty;

abstract class AbstractTestCase extends Testbench
{
    /** Get property value */
    protected function getPropertyValue($class, string $propertyName)
    {
        $props = $this->accessProperty(get_class($class), $propertyName);

        return $props->getValue($class);
    }

    /** Access private property */
    protected function accessProperty(string $classname, string $propertyName): ReflectionProperty
    {
        $reflector = new ReflectionClass($classname);

        $property = $reflector->getProperty($propertyName);

        $property->setAccessible(true);

        return $property;
    }
}
