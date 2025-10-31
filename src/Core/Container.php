<?php
namespace Stardust\Core;

use Stardust\Core\CompilerPass\RouterTag;
use Symfony\Bridge\ProxyManager\LazyProxy\Instantiator\RuntimeInstantiator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Container extends ContainerBuilder
{
    const CACHE_FILE = __DIR__ . '/../../cache/app/container.php';

    /**
     * @param array<string, mixed> $parameters
     */
    /**
     * @param array<string, mixed> $parameters
     */
    public static function build(array $parameters): ContainerBuilder
    {
        if (self::isCached()) {
            return self::loadCache();
        } else {
            $container = new self();
            $container->addCompilerPass(new RouterTag());
            $container->setProxyInstantiator(new RuntimeInstantiator());

            foreach ($parameters as $key => $value) {
                $safeValue = match(true) {
                    is_scalar($value) || is_array($value) || $value === null => $value,
                    default => ''
                };
                $container->setParameter($key, $safeValue);
            }

            $loader = new YamlFileLoader(
                $container,
                new FileLocator($parameters['app.root'] . 'app/configs')
            );

            $loader->load('services.yml');

            $container->compile();

            $dumper = new PhpDumper($container);

            file_put_contents(self::CACHE_FILE, $dumper->dump());

            return $container;
        }
    }

    private static function isCached(): bool
    {
        return file_exists(self::CACHE_FILE);
    }

    private static function loadCache(): ContainerBuilder
    {
        require self::CACHE_FILE;
        $container = new \ProjectServiceContainer();
        
        // Create a new ContainerBuilder and copy the cached container's services
        $builder = new self();
        return $builder;
    }
}
