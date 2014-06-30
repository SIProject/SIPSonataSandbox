<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use SIP\ResourceBundle\Entity\Text;

class LoadTextData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @return array
     */
    public function getTextDefinition()
    {
        $result = array();
        $TextBody = 'Содержимое';
        $arrayTextData = array(
            'Простой текст 1',
            'Простой текст 2',
            'Простой текст 3',
        );
        foreach($arrayTextData as $title) {
            $result[] = array(
                'title'     => $title,
                'body'      => $TextBody,
            );
        }
        return $result;
    }

    public function load(ObjectManager $manager)
    {
        foreach($this->getTextDefinition() as $textDesc) {
            $text = new Text();
            $text->setTitle($textDesc['title']);
            $text->setBody($textDesc['body']);
            $manager->persist($text);
        }
        $manager->flush();

        $this->addReference('text-element', $text);
    }

    public function getOrder()
    {
        return 15;
    }
}
