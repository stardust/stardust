<?php
namespace Stardust\Core\Controllers;

use Stardust\Core\Services\Configuration\ConfigurationLoaderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController
{
    private $configuration;

    private $session;

    public function __construct(ConfigurationLoaderService $configuration, Session $session)
    {
        $this->configuration = $configuration->values();
        $this->session       = $session;

        $defaultBag = new AttributeBag('default');
        $defaultBag->setName('Default');
        $this->session->registerBag($defaultBag);

        $this->session->start();
    }

    public function indexAction(Request $request): array
    {
        return [
            'accepted_format' => $request->headers->get('Accept'),
            'body'            => "Welcome to {$this->configuration->application->name} {$this->configuration->application->version} by {$this->configuration->application->author}!",
        ];
    }
}
