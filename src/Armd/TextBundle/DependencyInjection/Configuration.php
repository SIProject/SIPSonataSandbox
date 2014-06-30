<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace Armd\TextBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('armd_text');

        $rootNode
            ->children()
            ->scalarNode('model')->cannotBeEmpty()->end()
            ->scalarNode('admin')->defaultValue('Armd\\TextBundle\\Admin\\TextAdmin')->end()
            ->end();

        return $treeBuilder;
    }
}