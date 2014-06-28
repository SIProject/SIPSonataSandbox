<?php

namespace Armd\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TwigEnginePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('armd_cms.data_collector.twig.enabled')
            || !$container->getParameter('armd_cms.data_collector.twig.enabled')
        ) {
            return;
        }
        
        $container->setDefinition(
            'templating.engine.twig.decorated',
            $container->getDefinition('templating.engine.twig')
        );

        $container->setDefinition(
            'templating.engine.twig',
            new Definition(
                '%armd_cms.templating.engine.twig.class%',
                array(
                    new Reference('twig'),
                    new Reference('templating.engine.twig.decorated'),
                    new Reference('armd_cms.data_collector.twig'),
                )
            )
        );

    }

}