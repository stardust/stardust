<?php

namespace Stardust\Core\Configurations;

use Stardust\Core\Configuration;
use Symfony\Component\Yaml\Yaml;

class YamlConfiguration implements Configuration
{
    private mixed $configurationValues;

    private string $resource;

    /**
     * YamlConfiguration constructor.
     *
     * @param $resource
     */
    public function __construct($resource)
    {
        $this->resource            = $resource;
        $this->configurationValues = $this->parseConfiguration($resource);
    }

    public function extendsConfiguration(): bool
    {
        return array_key_exists('extends', $this->configurationValues);
    }

    public function path(): string
    {
        return dirname(dirname($this->resource)) . DIRECTORY_SEPARATOR;
    }

    public function parentEnvironment(): string|null
    {
        $parentEnvironment = null;
        if (array_key_exists('extends', $this->configurationValues)) {
            $parentEnvironment = $this->configurationValues['extends'];
        }

        return $parentEnvironment;
    }

    public function file(): string
    {
        return basename($this->resource);
    }

    public function values(): mixed
    {
        return $this->configurationValues;
    }

    /**
     * @param $resource
     *
     * @return mixed
     */
    private function parseConfiguration($resource)
    {
        return Yaml::parse(file_get_contents($resource));
    }
}
