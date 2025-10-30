<?php
namespace Stardust\Core\Listeners;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class StringResponseListener implements EventSubscriberInterface
{
    const CONTENT_TYPE = 'text/plain';

    /**
     * @param ViewEvent $event
     */
    public function onView(ViewEvent $event): void
    {
        $stringResponse = $event->getControllerResult();

        if (is_string($stringResponse)) {
            $response = new Response();
            $response->headers->set('Content-Type', self::CONTENT_TYPE);
            $response->setContent($stringResponse);
            $response->setStatusCode(Response::HTTP_OK);
            $response->setTtl(10);

            $event->setResponse($response);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return ['kernel.view' => 'onView'];
    }
}
