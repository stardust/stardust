<?php
namespace Stardust\Core\Listeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;

class TwigRendererListener implements EventSubscriberInterface
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

    private Environment $twig;

    /**
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $this->twig->addExtension(new DebugExtension());
    }

    /**
     * @param ViewEvent $event
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
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

            $loader = $this->twig->getLoader();
            if (method_exists($loader, 'prependPath')) {
                $loader->prependPath(dirname(__DIR__) . "/../$bundle/Resources");
            }

            $response = new Response(
                $this->twig->render(sprintf('/Views/%s/%s.%s.twig', $controllerName, $action, $format), $response)
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
