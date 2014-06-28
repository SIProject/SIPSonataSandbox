<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Armd\Bundle\CmsBundle\DependencyInjection\TweakPass;
use Armd\Bundle\CmsBundle\DependencyInjection\TwigEnginePass;

class ArmdCmsBundle extends Bundle
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TweakPass());
        $container->addCompilerPass(new TwigEnginePass());
    }
}
