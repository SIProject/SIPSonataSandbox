<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Builder implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param FactoryInterface $factory
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('sip_resource_about',
            array(
                'route'           => 'sip_staic_pages',
                'routeParameters' => array('slug' => 'about'),
            )
        );

        /*$calendar = $menu->addChild('sip_resource_calendar',
            array(
                'route' => 'sip_resource_calendar'
            )
        );
        $calendar->setCurrent($this->isCurrent($calendar));*/

        /*$foto = $menu->addChild('sip_resource_foto',
            array(
                'route' => 'sip_resource_foto'
            )
        );
        $foto->setCurrent($this->isCurrent($foto));*/

        $news = $menu->addChild('sip_resource_news',
            array(
                'route' => 'sip_resource_news'
            )
        );
        $news->setCurrent($this->isCurrent($news));

        $menu->addChild('sip_resource_presscenter',
            array(
                'route'           => 'sip_staic_pages',
                'routeParameters' => array('slug' => 'presscenter'),
            )
        );

        $menu->addChild('sip_resource_contacts',
            array(
                'route'           => 'sip_staic_pages',
                'routeParameters' => array('slug' => 'contacts'),
            )
        );

        $menu->addChild('sip_resource_partners',
            array(
                'route'           => 'sip_staic_pages',
                'routeParameters' => array('slug' => 'partners'),
            )
        );

        return $menu;
    }

    /**
     * @param \Knp\Menu\ItemInterface $item
     * @return bool|null
     */
    public function isCurrent(\Knp\Menu\ItemInterface $item)
    {
        /** @var \Symfony\Component\HttpFoundation\Request $request*/
        $request = $this->container->get('request');

        $regexp = str_replace('/', '\/', $item->getUri());
        if (preg_match("/^{$regexp}/", "{$request->getBaseUrl()}{$request->getPathInfo()}")) {
            return true;
        }

        return null;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}