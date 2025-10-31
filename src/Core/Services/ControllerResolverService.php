<?php

namespace Stardust\Core\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Webmozart\Assert\Assert;

/**
 * Resolves controllers defined as services (service:method notation).
 */
class ControllerResolverService implements ControllerResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ControllerResolverInterface
     */
    private $controllerResolver;

    /**
     * @param ContainerInterface          $container
     * @param ControllerResolverInterface $controllerResolver
     */
    public function __construct(ContainerInterface $container, ControllerResolverInterface $controllerResolver)
    {
        $this->container = $container;
        $this->controllerResolver = $controllerResolver;
    }

    public function getController(Request $request): callable|false
    {
        $controllerName = $request->attributes->get('_controller', '');
        Assert::string($controllerName);

        $parts = explode(':', $controllerName);

        if (2 !== count($parts)) {
            return $this->controllerResolver->getController($request);
        }

        $controller = $this->container->get($parts[0]);
        $method = $parts[1];

        if ($controller === null || !method_exists($controller, $method)) {
            return false;
        }

        return function(...$args) use ($controller, $method) {
            return $controller->$method(...$args);
        };
    }

    /**
     * @param callable $controller
     * @return array<mixed>
     */
    public function getArguments(Request $request, callable $controller): array
    {
        // Delegate to the decorated resolver if it supports it
        return $this->controllerResolver instanceof ArgumentResolverInterface
        ? $this->controllerResolver->getArguments($request, $controller)
        : [];
    }
}
