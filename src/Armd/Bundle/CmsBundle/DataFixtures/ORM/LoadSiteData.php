<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Armd\Bundle\CmsBundle\Entity\Site;


class LoadSiteData extends AbstractFixture implements OrderedFixtureInterface
{
    public function getMenuData()
    {
        return array(array('title' => 'Основной сайт', 'name' => 'main'));
    }

    public function load(ObjectManager $manager)
    {
        foreach ( $this->getMenuData() as $element ) {
            $site = new Site();
            $site->setTitle($element['title']);

            $this->addReference('site-' . $element['name'], $site);

            $manager->persist($site);
        }

        $manager->flush();
    }


    public function getOrder()
    {
        return 29;
    }
}