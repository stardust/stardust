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

    private $acceptedFormats = [
        'text/html',
        'application/json',
        'application/xml',
    ];

    /**
     * @var Environment
     */
    private $twig;

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

            $this->twig->getLoader()->prependPath(dirname(__DIR__) . "/../$bundle/Resources");

            $response = new Response(
                $this->twig->render(sprintf('/Views/%s/%s.%s.twig', $controllerName, $action, $format), $response)
            );

            $response->setTtl(10);

            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents() : array
    {
        return ['kernel.view' => 'onView'];
    }

    private function getController(ViewEvent $event)
    {
        return $event->getRequest()->attributes->get('_controller');
    }

    private function getBundle($definition)
    {
        $bundle = $this->matchExpressions([
            '/(?<=Stardust\\\\).*?(?=\\\\Controllers)/',
            '/(?<=stardust\.).*?(?=\.controllers)/',
        ], $definition);

        return ucfirst($bundle);
    }

    private function getControllerName($definition)
    {
        $controller = $this->matchExpressions([
            '/(?<=Controllers\\\\).*?(?=Controller)/',
            '/(?<=controllers\.).*?(?=_controller)/',
        ], $definition);

        return ucfirst($controller);
    }

    private function getAction($definition)
    {
        return $this->matchExpressions([
            '/(?<=::).*?(?=Action)/',
            '/(?<=:).*?(?=Action)/',
        ], $definition);
    }

    private function matchExpressions(array $expressions, $definition)
    {
        foreach ($expressions as $expression) {
            preg_match($expression, $definition, $match);
            if (!empty($match)) {
                break;
            }
        }

        return array_shift($match);
    }

    private function getResponseFormat($acceptedFormat)
    {
        $responseFormat = self::DEFAULT_FORMAT;
        if (in_array($acceptedFormat, $this->acceptedFormats, true)) {
            $responseFormat = explode('/', $acceptedFormat)[1];
        }

        return $responseFormat;
    }
}
