<?php

namespace Armd\ContentAbstractBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Armd\ContentAbstractBundle\DependencyInjection\Compiler\AddDependencyCallsCompilerPass;

class ArmdContentAbstractBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddDependencyCallsCompilerPass());        
    }
}
