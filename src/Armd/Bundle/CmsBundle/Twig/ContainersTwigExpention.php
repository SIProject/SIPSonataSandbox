<?php

namespace Armd\Bundle\CmsBundle\Twig;

use Twig_Extension;
use Twig_Function_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContainersTwigExpention extends Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $containerPoll = array();

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container) 
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions() 
    {
        return array(
            'getContainerContent'  => new Twig_Function_Method($this, 'getContentByContainer', array('is_safe' => array('html'))),
            'add_container'        => new Twig_Function_Method($this, 'addContainerPoll', array('is_safe' => array('html'))),
            'get_lost_containers'  => new Twig_Function_Method($this, 'getLostContainers', array('is_safe' => array('html')))
        );
    }

    /**
     * @param $containerName
     */
    public function addContainerPoll($containerName)
    {
        $this->containerPoll[] = $containerName;
    }

    /**
     * @param array $containers
     * @return array
     */
    public function getLostContainers(array $containers = null)
    {
        foreach ($this->containerPoll as $containerName) {
            if (isset($containers[$containerName])) {
                unset($containers[$containerName]);
            }
        }

        return $containers;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getContentByContainer($name)
    {
        return $this->container->get('armd_cms.container_collection_service')->getContainersContant($name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'armd_cms_content_twig_extension';
    }
}