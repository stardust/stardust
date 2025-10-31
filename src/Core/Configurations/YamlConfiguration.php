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
     */
    public function __construct(string $resource)
    {
        $this->resource            = $resource;
        $this->configurationValues = $this->parseConfiguration($resource);
    }

    public function extendsConfiguration(): bool
    {
        return is_array($this->configurationValues) && array_key_exists('extends', $this->configurationValues);
    }

    public function path(): string
    {
        return dirname(dirname($this->resource)) . DIRECTORY_SEPARATOR;
    }

    public function parentEnvironment(): string|null
    {
        $parentEnvironment = null;
        if (is_array($this->configurationValues) && array_key_exists('extends', $this->configurationValues)) {
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

    private function parseConfiguration(string $resource): mixed
    {
        $content = file_get_contents($resource);
        return $content !== false ? Yaml::parse($content) : null;
    }
}
