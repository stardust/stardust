<?php
namespace Stardust\Core\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;

        $defaultBag = new AttributeBag('default');
        $defaultBag->setName('Default');
        $this->session->registerBag($defaultBag);

        $this->session->start();
    }

    /**
     * @return array<string, string>
     */
    /**
     * @return array<string, string>
     */
    public function indexAction(Request $request): array
    {
        $acceptedFormat = $request->headers->get('Accept') ?? 'text/html';
        
        return [
            'accepted_format' => $acceptedFormat,
            'body'            => 'Welcome to Stardust!',
        ];
    }
}
