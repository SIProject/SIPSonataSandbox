<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class Configuration implements ConfigurationInterface
{
    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('armd_cms', 'array');

        $rootNode
            ->children()
                ->arrayNode('ignore_route_patterns')
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('usagetype')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('class')->defaultValue('Armd\Bundle\CmsBundle\UsageType\BaseUsageService')->end()
                    ->end()
                ->end()
                ->arrayNode('profiler')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('twig')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->booleanNode('display_in_wdt')->defaultTrue()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        $this->addTemplateSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node
     */
    private function addTemplateSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('source_bundle')->defaultValue('ArmdCmsBundle')->end()
                        ->arrayNode('layouts')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('title')->end()
                                    ->booleanNode('default')->defaultValue(false)->end()
                                ->end()
                            ->end()
                            ->defaultValue(array('main' => array('title' => 'Main layout', 'default' => true)))
                        ->end()
                        ->arrayNode('modules')
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->arrayNode('templates')
                                        ->useAttributeAsKey('name')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('title')->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
