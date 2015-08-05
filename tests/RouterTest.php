<?php

namespace FuckingSmallTest;

use FuckingSmall\Dispatching\Router;
use FuckingSmall\Http\Request;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testSimpleRouteMatching()
    {
        $request = $this->prophesize(Request::class);
        $request->getUri()
            ->shouldBeCalledTimes(3)
            ->willReturn('/bob');

        $router = new Router();

        $router->attach('foo', '/foo', 'SimpleController::indexAction');
        $router->attach('bar', '/bar', 'SimpleController::indexAction');
        $router->attach('bob', '/bob', 'SimpleController::indexAction');

        $payload = $router->resolve($request->reveal());

        $this->assertEquals('SimpleController', $payload['_controller']);
        $this->assertEquals('indexAction', $payload['_method']);
    }

    public function testRouteMatchingWithTokens()
    {
        $request = $this->prophesize(Request::class);
        $request->getUri()
                ->shouldBeCalledTimes(1)
                ->willReturn('/foo/14');

        $router = new Router();

        $router->attach('foo', '/foo/{id}', 'SimpleController::indexAction');

        $payload = $router->resolve($request->reveal());

        $this->assertEquals('SimpleController', $payload['_controller']);
        $this->assertEquals('indexAction', $payload['_method']);
        $this->assertEquals(14, $payload['id']);
    }

    public function testRouteMatchingWithOptional()
    {
        $request = $this->prophesize(Request::class);
        $request->getUri()
                ->shouldBeCalledTimes(1)
                ->willReturn('/foo');

        $router = new Router();

        $router->attach('foo', '/foo/{id}', 'SimpleController::indexAction', ['defaults' => ['id' => 14]]);

        $payload = $router->resolve($request->reveal());

        $this->assertEquals('SimpleController', $payload['_controller']);
        $this->assertEquals('indexAction', $payload['_method']);
        $this->assertEquals(14, $payload['id']);
    }

    public function testRouteMatchingIntFilter()
    {
        $request = $this->prophesize(Request::class);
        $request->getUri()
                ->shouldBeCalledTimes(1)
                ->willReturn('/foo/14');

        $router = new Router();

        $router->attach('foo', '/foo/{id}', 'SimpleController::indexAction', ['filters' => ['id' => '{int}']]);

        $payload = $router->resolve($request->reveal());

        $this->assertEquals('SimpleController', $payload['_controller']);
        $this->assertEquals('indexAction', $payload['_method']);
        $this->assertEquals(14, $payload['id']);
    }

    public function testRouteFailsToMatchIntFilter()
    {
        $request = $this->prophesize(Request::class);
        $request->getUri()
                ->shouldBeCalledTimes(2)
                ->willReturn('/foo/foo');

        $router = new Router();

        $router->attach('foo_int', '/foo/{id}', 'SimpleController::indexAction', ['filters' => ['id' => '{int}']]);
        $router->attach('foo_string', '/foo/{id}', 'SimpleController::indexAction');

        $payload = $router->resolve($request->reveal());

        $this->assertEquals('SimpleController', $payload['_controller']);
        $this->assertEquals('indexAction', $payload['_method']);
        $this->assertEquals('foo_string', $payload['_route']);
        $this->assertEquals('foo', $payload['id']);
    }

    public function testCanGenerateSimpleUrl()
    {
        $router = new Router();

        $router->attach('foo', '/foo/bar', 'SimpleController::indexAction');
        $url = $router->gentUrl('foo');

        $this->assertEquals('/foo/bar', $url);
    }

    public function testCanGenerateUrlWithSimpleToken()
    {
        $router = new Router();

        $router->attach('foo', '/foo/{id}', 'SimpleController::indexAction');
        $url = $router->gentUrl('foo', ['id' => 14]);

        $this->assertEquals('/foo/14', $url);
    }

    public function testCanGenerateUrlWithDefaultToken()
    {
        $router = new Router();

        $router->attach('foo', '/foo/{id}/{some_default}', 'SimpleController::indexAction', [
            'defaults' => ['some_default' => 'some-value']]
        );
        $url = $router->gentUrl('foo', ['id' => 14]);

        $this->assertEquals('/foo/14/some-value', $url);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsWhenNotEnoughParamsPassedToUrl()
    {
        $router = new Router();

        $router->attach('foo', '/foo/{id}', 'SimpleController::indexAction');
        $router->gentUrl('foo');
    }

    public function testCanMountSimple()
    {
        $request = $this->prophesize(Request::class);
        $request->getUri()
                ->shouldBeCalledTimes(3)
                ->willReturn('/foo/car');

        $router = new Router();

        $router->mount('/foo', [
            ['a', '/aar', 'SimpleController::aarAction'],
            ['b', '/bar', 'SimpleController::barAction'],
            ['c', '/car', 'SimpleController::carAction'],
        ]);

        $payload = $router->resolve($request->reveal());

        $this->assertEquals('SimpleController', $payload['_controller']);
        $this->assertEquals('carAction', $payload['_method']);
    }

    public function testCanMountMoreComplex()
    {
        $request = $this->prophesize(Request::class);
        $request->getUri()
                ->shouldBeCalledTimes(2)
                ->willReturn('/foo/bar/bob');

        $router = new Router();

        $router->mount('/foo', [
            ['a', '/bar/{id}', 'SimpleController::barAction', ['filters' => ['id' => '{int}']]],
            ['b', '/bar/{id}', 'SimpleController::barAction'],
        ]);

        $payload = $router->resolve($request->reveal());

        $this->assertEquals('SimpleController', $payload['_controller']);
        $this->assertEquals('barAction', $payload['_method']);
        $this->assertEquals('b', $payload['_route']);
    }
}