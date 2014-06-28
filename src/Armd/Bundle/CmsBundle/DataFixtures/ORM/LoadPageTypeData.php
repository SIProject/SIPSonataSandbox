<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Armd\Bundle\CmsBundle\Entity\PageType;


class LoadPageTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $pageType = new PageType();
        $pageType->setTitle('Тип главной страницы');

        $manager->persist($pageType);
        $manager->flush();

        $this->addReference('page-type-main', $pageType);
    }

    public function getOrder()
    {
        return 10;
    }
}
