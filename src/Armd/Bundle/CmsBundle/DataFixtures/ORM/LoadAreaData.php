<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Armd\Bundle\CmsBundle\Entity\Area;


class LoadAreaData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    public function load(ObjectManager $manager)
    {
        $areas = array('content', 'menu', 'header', 'footer');

        $this->em = $manager;

        foreach ($areas as $name) {
            $this->addReference("area-{$name}", $this->createArea($name));
        }

        $this->em->flush();
    }

    public function createArea($name)
    {
        $container = new Area();
        $container->setName($name);
        $container->setTitle($name);
        $this->em->persist($container);

        return $container;
    }

    public function getOrder()
    {
        return 20;
    }
}
