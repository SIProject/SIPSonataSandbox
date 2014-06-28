<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Armd\Bundle\CmsBundle\Entity\Page;


class LoadPageData extends AbstractFixture implements OrderedFixtureInterface
{
    public function getMenuData()
    {
        return array(array('title' => 'Главная', 'slug' => 'ru', 'pageType' => 'page-type-main', 'site' => 'site-main', 'name' => 'main'));
    }

    public function load(ObjectManager $manager)
    {
        foreach ( $this->getMenuData() as $element ) {
            $page = new Page();
            $page->setTitle($element['title']);
            $page->setSlug($element['slug']);
            $page->setMenuEnabled(true);

            if ( $this->hasReference($element['pageType']) ) {
                $page->setPageType($this->getReference($element['pageType']));
            }

            if ( $this->hasReference($element['site']) ) {
                $page->setSite($this->getReference($element['site']));
            }

            if ( isset($element['parent']) && $this->hasReference($element['parent']) ) {
                $page->setParent($this->getReference($element['parent']));
            }

            $this->addReference('page-' . $element['name'], $page);

            $manager->persist($page);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 30;

    }
}
