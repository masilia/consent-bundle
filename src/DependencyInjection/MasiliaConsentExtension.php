<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MasiliaConsentExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Set parameters
        $container->setParameter('masilia_consent.storage', $config['storage']);
        $container->setParameter('masilia_consent.logging', $config['logging']);
        $container->setParameter('masilia_consent.api', $config['api']);
        $container->setParameter('masilia_consent.admin', $config['admin']);

        // Load services
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');
    }

    public function getAlias(): string
    {
        return 'masilia_consent';
    }
}
