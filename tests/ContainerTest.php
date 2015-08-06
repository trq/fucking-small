<?php

namespace FuckingSmallTest;

use FuckingSmall\IoC\Container;
use FuckingSmallTest\Fixture\SimpleService;
use FuckingSmallTest\Fixture\SimpleServiceInterface;
use FuckingSmallTest\Fixture\ComplexService;
use FuckingSmallTest\Fixture\ParentService;
use FuckingSmallTest\Fixture\ChildService;

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

    public function testCanResolveParamsFromTemplate()
    {
        $container = new Container();

        $container->template(ParentService::class, ['foo' => 'bar']);

        $child = $container->resolve(ChildService::class);

        $this->assertEquals('bar', $child->getFoo());
    }

    public function testCanFindServicesByAttributes()
    {
        $container = new Container();

        $container->attach('foo', function() {}, ['tags' => ['foo']]);
        $container->attach('bar', function() {}, ['tags' => ['foo']]);
        $container->attach('bob', function() {}, ['tags' => ['foo']]);

        $services = $container->findByAttribute('tags', 'foo');

        $this->assertCount(3, $services);
    }

    public function testCanEditServiceAttributes()
    {
        $container = new Container();

        $container->attach('foo', function() {}, ['tags' => ['foo']]);

        $tags = $container->getAttribute('foo', 'tags');

        $this->assertEquals(['foo'], $tags);

        $tags[] = 'bar';

        $container->setAttribute('foo', 'tags', $tags);

        $tags = $container->getAttribute('foo', 'tags');

        $this->assertEquals(['foo', 'bar'], $tags);
    }
}