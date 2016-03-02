<?php

namespace Stardust\Core\Configurations;

use Stardust\Core\Configuration;
use Symfony\Component\Yaml\Yaml;

class YamlConfiguration implements Configuration
{
    /**
     * @var mixed
     */
    private $configurationValues;

    /**
     * @var string
     */
    private $resource;

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

    /**
     * @return bool
     */
    public function extendsConfiguration()
    {
        return array_key_exists('extends', $this->configurationValues);
    }

    /**
     * @return string
     */
    public function path()
    {
        return dirname(dirname($this->resource)) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return string|null
     */
    public function parentEnvironment()
    {
        $parentEnvironment = null;
        if (array_key_exists('extends', $this->configurationValues)) {
            $parentEnvironment = $this->configurationValues['extends'];
        }

        return $parentEnvironment;
    }

    /**
     * @return string
     */
    public function file()
    {
        return basename($this->resource);
    }

    /**
     * @return mixed
     */
    public function values()
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
