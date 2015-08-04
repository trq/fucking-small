<?php

namespace FuckingSmallTest;

use FuckingSmall\Container;
use FuckingSmallTest\Fixture\SimpleService;
use FuckingSmallTest\Fixture\SimpleServiceInterface;
use FuckingSmallTest\Fixture\ComplexService;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanAttacheAndResolveService()
    {
        $container = new Container();

        $container->attach('some_service', function() {
            $service = new \StdClass();
            $service->foo = 'bar';

            return $service;
        });

        $service = $container->resolve('some_service');

        $this->assertEquals('bar', $service->foo);
    }

    public function testCanAutoResolve()
    {
        $container = new Container();

        $service = $container->resolve(SimpleService::class);

        $this->assertInstanceOf(SimpleService::class, $service);
    }

    public function testCanAutoResolveWithDependencies()
    {
        $container = new Container();

        $service = $container->resolve(ComplexService::class);

        $this->assertInstanceOf(ComplexService::class, $service);
    }

    public function testCanStoreASingleton()
    {
        $container = new Container();

        $container->attach('some_singleton', function() {
            static $object;

            if (null === $object) {
                $object = new \StdClass();
            }

            return $object;
        });

        $s1 = $container->resolve('some_singleton');
        $s2 = $container->resolve('some_singleton');

        $this->assertSame($s1, $s2);
    }

    public function testCanAutoResolveAlias()
    {
        $container = new Container();

        $container->alias('foo', SimpleService::class);
        $service = $container->resolve('foo');

        $this->assertInstanceOf(SimpleService::class, $service);
    }

    public function testCanAutoResolveInterfaceAlias()
    {
        $container = new Container();

        $container->alias(simpleServiceInterface::class, SimpleService::class);
        $service = $container->resolve(SimpleServiceInterface::class);

        $this->assertInstanceOf(SimpleService::class, $service);
    }

    public function testCanResolveAliasFromContainer()
    {
        $container = new Container();

        $container->alias('foo', 'bar', function () {
            return new \StdClass();
        });

        $foo = $container->resolve('foo');
        $bar = $container->resolve('bar');

        $this->assertInstanceOf(\StdClass::class, $foo);
        $this->assertInstanceOf(\StdClass::class, $bar);
    }
}