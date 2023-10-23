<?php

namespace spec\Stardust\Core\Configurations;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use Stardust\Core\Configuration;
use Stardust\Core\Configurations\YamlConfiguration;
use PhpSpec\ObjectBehavior;

class YamlConfigurationSpec extends ObjectBehavior
{
    /**
     * @var vfsStreamDirectory
     */
    private $vfssd;

    function let()
    {
        $this->createTestConfigurationWithoutInheritance();

        $this->beConstructedWith(vfsStream::url('vfssd/config/env/configuration.yml'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(YamlConfiguration::class);
    }

    function it_implements_configuration()
    {
        $this->shouldImplement(Configuration::class);
    }

    function it_detects_lack_of_inheritance()
    {
        $this->extendsConfiguration()->shouldReturn(false);
    }

    function it_detects_inheritance()
    {
        $this->createTestConfigurationWithInheritance();

        $this->beConstructedWith(vfsStream::url('vfssd/config/env/configuration.yml'));

        $this->extendsConfiguration()->shouldReturn(true);
    }

    function it_returns_the_configuration_file_path()
    {
        $this->path()->shouldBe('vfs://vfssd/config/');
    }

    function it_returns_null_when_parent_environment_is_not_present()
    {
        $this->parentEnvironment()->shouldBeNull();
    }

    function it_return_the_parent_environment_when_present()
    {
        $this->createTestConfigurationWithInheritance();

        $this->beConstructedWith(vfsStream::url('vfssd/config/env/configuration.yml'));

        $this->parentEnvironment()->shouldBe('parentenv');
    }

    function it_returns_the_configuration_file_name()
    {
        $this->file()->shouldBe('configuration.yml');
    }

    function it_returns_the_configuration_values_as_array()
    {
        $this->values()->shouldBeArray();
        $this->values()->shouldHaveKey('configuration');
    }

    private function createTestConfigurationWithoutInheritance()
    {
        $content = <<<CONTENT
configuration:
  name: Fake app
  version: 0.0.0
  author: Fake Author 
CONTENT;

        $this->vfssd = vfsStream::setup('vfssd', null, [
            'config/env/configuration.yml' => $content
        ]);
    }

    private function createTestConfigurationWithInheritance()
    {
        $parentContent = <<<PARENT_CONTENT
configuration:
  name: Parent Fake app
  version: 0.0.0
  author: Parent Fake Author
PARENT_CONTENT;

        $content = <<<CONTENT
extends: parentenv
        
configuration:
  author: Fake Author 
CONTENT;

        $this->vfssd = vfsStream::setup('vfssd', null, [
            'config/parentenv/configuration.yml' => $parentContent,
            'config/env/configuration.yml'       => $content
        ]);
    }

}
