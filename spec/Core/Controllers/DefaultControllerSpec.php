<?php

namespace spec\Stardust\Core\Controllers;

use Stardust\Core\Controllers\DefaultController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Stardust\Core\Services\Configuration\ConfigurationLoaderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;

class DefaultControllerSpec extends ObjectBehavior
{
    function let(ConfigurationLoaderService $configuration, Session $session)
    {
        $testConfiguration = $this->getTestConfiguration();

        $configuration->values()->willReturn($testConfiguration);
        $session->start()->willReturn(true);
        $session->registerBag(Argument::type(AttributeBag::class))->shouldBeCalled();

        $this->beConstructedWith($configuration, $session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DefaultController::class);
    }

    function it_responds_to_index_action()
    {
        $request = new Request();
        $request->headers->set('accept', 'application/json');

        $response = $this->indexAction($request);

        $response->shouldHaveKey('accepted_format');
        $response->shouldHaveKey('body');
    }

    /**
     * @return \stdClass
     */
    private function getTestConfiguration()
    {
        $testConfigurationContent          = new \stdClass();
        $testConfigurationContent->name    = 'Test App Name';
        $testConfigurationContent->version = 'Test App Version';
        $testConfigurationContent->author  = 'Test Author Name';

        $testConfiguration              = new \stdClass();
        $testConfiguration->application = $testConfigurationContent;

        return $testConfiguration;
    }
}
