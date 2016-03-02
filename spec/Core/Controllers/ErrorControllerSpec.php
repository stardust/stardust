<?php

namespace spec\Stardust\Core\Controllers;

use Stardust\Core\Controllers\ErrorController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Debug\Exception\FlattenException;

class ErrorControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ErrorController::class);
    }

    function it_respond_to_exception_action(FlattenException $exception)
    {
        $exception->getMessage()->willReturn('A random error message');
        $exception->getStatusCode()->willReturn(500);

        $response = $this->exceptionAction($exception);

        $response->shouldHaveType('Symfony\Component\HttpFoundation\Response');
    }
}
