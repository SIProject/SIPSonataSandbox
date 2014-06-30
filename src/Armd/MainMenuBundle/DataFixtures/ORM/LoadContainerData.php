<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\ResourceBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadContainerData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        foreach ( $this->getData() as $element ) {
            if ( $this->hasReference($element['сontainer']) ) {
                /**
                 * @var \Armd\Bundle\CmsBundle\Entity\Container $container
                 */
                $container = $this->getReference($element['сontainer']);

                $container->setUsageService($element['usageService']);
                $container->setUsageType($element['usageType']);
                $container->setSettings($element['settings']);

                $manager->persist($container);
            }
        }

        $manager->flush();
    }

    public function getData()
    {
        return array(array('сontainer' => 'container-menu',
            'usageService' => 'armd_main_menu',
            'usageType' => 'toplevels',
            'settings' => array('depth' => 5, 'level' => 2)));
    }

    public function getOrder()
    {
        return 112;
    }

}