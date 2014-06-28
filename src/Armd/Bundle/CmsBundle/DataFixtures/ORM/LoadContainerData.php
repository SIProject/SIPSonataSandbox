<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Armd\Bundle\CmsBundle\Entity\Container;


class LoadContainerData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Armd\Bundle\CmsBundle\Entity\PageType
     */
    protected $pageType;

    public function load(ObjectManager $manager)
    {
        $containers = array(
            'content'   => true,
            'menu'      => false,
            'header'    => false,
            'footer'    => false,
        );
        
        $this->em = $manager;

        if (!$this->hasReference('page-type-main')) {
            return;
        }

        $this->pageType = $this->getReference('page-type-main');

        foreach ($containers as $areaName => $is_main)
        {
            $container = $this->createContainer($areaName, $is_main);
            $this->addReference("container-{$areaName}", $container);
            $this->pageType->addContainer($container);
        }

        $this->em->persist($this->pageType);

        $this->em->flush();
    }
    
    public function createContainer($areaName, $is_main)
    {
        $container = new Container();
        $container->setArea($this->getReference("area-{$areaName}"));
        $container->setIsMain($is_main);
        $this->em->persist($container);
        
        return $container;
    }

    public function getOrder()
    {
        return 22;
    }
}
