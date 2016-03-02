<?php
namespace Stardust\Core\Controllers;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;

class ErrorController
{
    public function errorAction(FlattenException $exception): Response
    {
        $message = "Something went wrong! ({$exception->getMessage()})";

        return new Response($message, $exception->getStatusCode());
    }

}
