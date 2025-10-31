<?php

namespace Stardust\Core\Services\Configuration;

use Exception;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Finder\Finder;

class ConfigurationLoaderService
{
    private string $environment;

    private DelegatingLoader $loader;

    private mixed $configurationValues = [];

    public function __construct(DelegatingLoader $loader, string $environment)
    {
        $this->environment = $environment;
        $this->loader      = $loader;
    }

    /**
     * @param mixed $directories
     */
    public function load(mixed $directories): bool
    {
        try {
            $configurationValues = [];
            if (!is_iterable($directories)) {
                return false;
            }
            
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

            $this->configurationValues = json_decode(json_encode($configurationValues) ?: '{}');

            return true;
        } catch (\Throwable $exception) {
            throw $exception;
        }
    }

    public function values(): mixed
    {
        return $this->configurationValues;
    }
}
