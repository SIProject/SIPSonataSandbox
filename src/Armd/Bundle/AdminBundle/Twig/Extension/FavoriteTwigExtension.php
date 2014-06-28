<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\AdminBundle\Twig\Extension;
use Twig_Extension;
use Twig_Function_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FavoriteTwigExtension extends Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getFunctions() {
        return array(
            'isFavorite'  => new Twig_Function_Method($this, 'isFavorite'),
        );
    }

    /**
     * @param $serviceId
     * @return bool
     */
    public function isFavorite($serviceId)
    {
        if ($this->container->get('doctrine')->getRepository('ArmdAdminBundle:Favorites')->findOneByServiceId($serviceId)) {
            return true;
        }
        return false;
    }

    public function getName()
    {
        return 'favorite_extension';
    }
}
