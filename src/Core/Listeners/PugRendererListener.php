<?php

namespace Stardust\Core\Listeners;

use Exception;
use Pug\Pug;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class PugRendererListener implements EventSubscriberInterface
{
    public const DEFAULT_FORMAT = 'html';

    /**
     * @var array<string>
     */
    private array $acceptedFormats = [
        'text/html',
        'application/json',
        'application/xml',
    ];

    /**
     * @param ViewEvent $event
     * @throws Exception
     */
    public function onView(ViewEvent $event): void
    {
        $response = $event->getControllerResult();

        if (is_array($response)) {
            $controllerDefinition = $this->getController($event);

            $bundle         = $this->getBundle($controllerDefinition);
            $controllerName = $this->getControllerName($controllerDefinition);
            $action         = $this->getAction($controllerDefinition);
            $format         = $this->getResponseFormat($response['accepted_format']);

            $pug = new Pug([
                'basedir'     => dirname(__DIR__) . "/../$bundle/Resources",
                'prettyprint' => true,
                'extension'   => '.pug',
                'cache' => '%app.root%/cache/pug',
            ]);

            $basedir = $pug->getOption('basedir');
            $basedirStr = is_string($basedir) ? $basedir : '';
            
            $response = new Response(
                $pug->render(sprintf('%s/Views/%s/%s.%s.pug', $basedirStr, $controllerName, $action, $format), [
                    'content' => $response['content'] ?? '',
                ])
            );

            $response->setTtl(10);

            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return ['kernel.view' => 'onView'];
    }

    private function getController(ViewEvent $event): mixed
    {
        return $event->getRequest()->attributes->get('_controller');
    }

    private function getBundle(mixed $definition): string
    {
        $bundle = $this->matchExpressions([
            '/(?<=Stardust\\\\).*?(?=\\\\Controllers)/',
            '/(?<=stardust\.).*?(?=\.controllers)/',
        ], $definition);

        return ucfirst($bundle);
    }

    private function getControllerName(mixed $definition): string
    {
        $controller = $this->matchExpressions([
            '/(?<=Controllers\\\\).*?(?=Controller)/',
            '/(?<=controllers\.).*?(?=_controller)/',
        ], $definition);

        return ucfirst($controller);
    }

    private function getAction(mixed $definition): string
    {
        return $this->matchExpressions([
            '/(?<=::).*?(?=Action)/',
            '/(?<=:).*?(?=Action)/',
        ], $definition);
    }

    /**
     * @param array<string> $expressions
     */
    private function matchExpressions(array $expressions, mixed $definition): string
    {
        $match = [];
        $definitionStr = is_string($definition) ? $definition : '';
        foreach ($expressions as $expression) {
            preg_match($expression, $definitionStr, $match);
            if (!empty($match)) {
                break;
            }
        }

        return array_shift($match) ?? '';
    }

    private function getResponseFormat(mixed $acceptedFormat): string
    {
        $responseFormat = self::DEFAULT_FORMAT;
        if (in_array($acceptedFormat, $this->acceptedFormats, true)) {
            $responseFormat = explode('/', $acceptedFormat)[1];
        }

        return $responseFormat;
    }
}
