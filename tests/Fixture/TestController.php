<?php

namespace FuckingSmallTest\Fixture;

class TestController
{
    public function fooAction()
    {
        return "this is foo";
    }

    public function barAction(SimpleService $ss)
    {
        return $ss->getSomeText();
    }

    public function payloadAction(SimpleService $ss, $id)
    {
        return $ss->getSomeText() . $id;
    }

    public function reversePayloadAction($id, SimpleService $ss)
    {
        return $ss->getSomeText() . $id;
    }

    public function defValueAction($foo = true)
    {
        return $foo;
    }
}