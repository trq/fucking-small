<?php

namespace FuckingSmallTest;

use FuckingSmall\IoC\Container;
use FuckingSmall\Dispatching\Dispatcher;
use FuckingSmallTest\Fixture\TestController;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testCanDispatchSimpleRequest()
    {
        $payload = [
            '_controller' => TestController::class,
            '_method' => 'fooAction'
        ];

        $dispatcher = new Dispatcher();
        $result = $dispatcher->dispatch(new Container(), $payload);

        $this->assertEquals('this is foo', $result);
    }

    public function testCanDispatchRequestToMethodsWithDependencies()
    {
        $payload = [
            '_controller' => TestController::class,
            '_method' => 'barAction'
        ];

        $dispatcher = new Dispatcher();
        $result = $dispatcher->dispatch(new Container(), $payload);

        $this->assertEquals('this is some text', $result);
    }

    public function testCanDispatchRequestToMethodsWithDependenciesAndPayload()
    {
        $payload = [
            '_controller' => TestController::class,
            '_method' => 'payloadAction',
            'id' => 88
        ];

        $dispatcher = new Dispatcher();
        $result = $dispatcher->dispatch(new Container(), $payload);

        $this->assertEquals('this is some text88', $result);
    }

    public function testPayloadOrderNotImportant()
    {
        $payload = [
            '_controller' => TestController::class,
            '_method' => 'reversePayloadAction',
            'id' => 88
        ];

        $dispatcher = new Dispatcher();
        $result = $dispatcher->dispatch(new Container(), $payload);

        $this->assertEquals('this is some text88', $result);
    }

    public function testDefaultValueWorks()
    {
        $payload = [
            '_controller' => TestController::class,
            '_method' => 'defValueAction',
        ];

        $dispatcher = new Dispatcher();
        $result = $dispatcher->dispatch(new Container(), $payload);

        $this->assertTrue($result);
    }

    public function testDefaultValueOverwritten()
    {
        $payload = [
            '_controller' => TestController::class,
            '_method' => 'defValueAction',
            'foo' => false
        ];

        $dispatcher = new Dispatcher();
        $result = $dispatcher->dispatch(new Container(), $payload);

        $this->assertFalse($result);
    }
}