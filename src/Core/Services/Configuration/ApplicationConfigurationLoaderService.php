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
     * @return array<string, mixed>
     */
    public function load(mixed $resource, string $type = null): array
    {
        $resourceStr = is_string($resource) ? $resource : '';
        $configuration = $this->getConfiguration($resourceStr);

        $configurationValues = $configuration->values();
        if ($configuration->extendsConfiguration()) {
            $configurationPath = $configuration->path();
            $parentEnvironment = $configuration->parentEnvironment();
            $configurationFile = $configuration->file();

            $parentConfiguration = $this->getConfiguration($configurationPath . $parentEnvironment . $configurationFile);

            $parentValues = $parentConfiguration->values();
            $currentValues = $configuration->values();
            
            if (is_array($parentValues) && is_array($currentValues)) {
                $configurationValues = array_replace_recursive($parentValues, $currentValues);
            } else {
                $configurationValues = $currentValues;
            }

            if (is_array($configurationValues)) {
                unset($configurationValues['extends']);
            }
        }

        return $this->processor->processConfiguration($this->configDefinition, [$configurationValues]);
    }

    public function supports(mixed $resource, string $type = null): bool
    {
        return is_string($resource) && 'application' === pathinfo(
            $resource,
            PATHINFO_FILENAME
        );
    }

    private function getConfiguration(string $resource): YamlConfiguration
    {
        return new YamlConfiguration($resource);
    }
}
