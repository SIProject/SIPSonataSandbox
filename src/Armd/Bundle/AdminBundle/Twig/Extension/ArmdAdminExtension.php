<?php

namespace Armd\Bundle\AdminBundle\Twig\Extension;

use \Twig_Extension;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ArmdAdminExtension extends Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return 'armd.hostnameExtension';
    }

    public function getGlobals()
    {
        return array(
            'defaults' => array(
                'common' => '/bundles/armdadmin',
                'macros' => 'ArmdAdminBundle::macro',
                'layouts' => 'ArmdAdminBundle::layout',
            ),
        );
    }

}