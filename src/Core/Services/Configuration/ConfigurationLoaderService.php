<?php

namespace Stardust\Core\Services\Configuration;

use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Finder\Finder;

class ConfigurationLoaderService
{
    private $environment;

    /**
     * @var DelegatingLoader
     */
    private $loader = null;

    private $configurationValues = [];

    public function __construct(DelegatingLoader $loader, $environment)
    {
        $this->environment = $environment;
        $this->loader      = $loader;
    }

    /**
     * Loads a resource.
     *
     * @param $directories
     * @return bool
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     * @throws \Exception
     */
    public function load($directories)
    {
        try {
            $configurationValues = [];
            foreach ($directories as $directory) {
                $location = $directory . DIRECTORY_SEPARATOR . $this->environment;
                foreach (Finder::create()->files()->in($location)->name('*.yml') as $file) {
                    if ($this->loader->supports($file->getPathname())) {
                        $currentConfigurationValues = $this->loader->load($file->getPathname());

                        $configurationValues = array_merge($configurationValues, [
                            pathinfo($file->getBasename(), PATHINFO_FILENAME) => $currentConfigurationValues,
                        ]);
                    }
                }
            }

            $this->configurationValues = json_decode(json_encode($configurationValues));

            return true;
        } catch (FileLoaderLoadException $fileLoaderLoadException) {
            throw $fileLoaderLoadException;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function values()
    {
        return $this->configurationValues;
    }
}
