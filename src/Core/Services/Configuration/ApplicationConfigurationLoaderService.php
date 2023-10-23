<?php

namespace Stardust\Core\Services\Configuration;

use Stardust\Core\Configurations\YamlConfiguration;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Yaml;

class ApplicationConfigurationLoaderService extends FileLoader
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var ConfigurationInterface
     */
    private $configDefinition;

    /**
     * CacheConfigurationLoaderService constructor.
     * @param Processor $processor
     * @param ConfigurationInterface $configDefinition
     */
    public function __construct(Processor $processor, ConfigurationInterface $configDefinition)
    {
        $this->processor        = $processor;
        $this->configDefinition = $configDefinition;
    }

    /**
     * Loads a resource.
     *
     * @param $resource
     * @param null $type
     * @return array
     */
    public function load($resource, $type = null)
    {
        $configuration = $this->getConfiguration($resource);

        $configurationValues = $configuration->values();
        if ($configuration->extendsConfiguration()) {
            $configurationPath = $configuration->path();
            $parentEnvironment = $configuration->parentEnvironment();
            $configurationFile = $configuration->file();

            $parentConfiguration = $this->getConfiguration($configurationPath . $parentEnvironment . $configurationFile);

            $configurationValues = array_replace_recursive($parentConfiguration->values(), $configuration->values());

            unset($configurationValues['extends']);
        }

        return $this->processor->processConfiguration($this->configDefinition, $configurationValues);
    }

    /**
     * Returns whether this class supports the given resource.
     *
     * @param mixed $resource A resource
     * @param string|null $type The resource type or null if unknown
     *
     * @return bool True if this class supports the given resource, false otherwise
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'application' === pathinfo(
            $resource,
            PATHINFO_FILENAME
        );
    }

    /**
     * @param $resource
     * @return mixed
     */
    private function getConfiguration($resource)
    {
        return new YamlConfiguration($resource);
    }
}
