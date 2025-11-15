<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('masilia_consent');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode('storage')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('cookie_name')
                            ->defaultValue('masilia_consent')
                            ->info('Name of the cookie storing user consent')
                        ->end()
                        ->integerNode('cookie_lifetime')
                            ->defaultValue(365)
                            ->info('Cookie lifetime in days')
                        ->end()
                        ->scalarNode('cookie_path')
                            ->defaultValue('/')
                        ->end()
                        ->scalarNode('cookie_domain')
                            ->defaultNull()
                        ->end()
                        ->booleanNode('cookie_secure')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('cookie_http_only')
                            ->defaultTrue()
                        ->end()
                        ->enumNode('cookie_same_site')
                            ->values(['lax', 'strict', 'none'])
                            ->defaultValue('lax')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('logging')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                            ->info('Enable consent logging for GDPR compliance')
                        ->end()
                        ->booleanNode('log_ip_address')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('log_user_agent')
                            ->defaultTrue()
                        ->end()
                        ->booleanNode('anonymize_ip')
                            ->defaultFalse()
                            ->info('Anonymize last octet of IP address')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('api')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('base_path')
                            ->defaultValue('/api/consent')
                        ->end()
                        ->booleanNode('cors_enabled')
                            ->defaultFalse()
                        ->end()
                        ->arrayNode('cors_origins')
                            ->scalarPrototype()->end()
                            ->defaultValue([])
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('admin')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->defaultTrue()
                        ->end()
                        ->scalarNode('route_prefix')
                            ->defaultValue('/admin/consent')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
