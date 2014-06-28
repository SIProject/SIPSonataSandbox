<?php

namespace Armd\ContentAbstractBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AddDependencyCallsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        $pool = $container->getDefinition('armd.content.pool');
        
        $services = array_keys($container->findTaggedServiceIds('armd.content.admin'));        
        $pool->addMethodCall('setAdminServiceIds', array($services));        
    }
}
