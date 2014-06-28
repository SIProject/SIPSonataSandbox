<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Tests\Model\Theme;

use PHPUnit_Framework_TestCase;

use Armd\Bundle\CmsBundle\Model\Theme;

class TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testIsEquals()
    {
        $template = new Theme\Template();
        $template->setName('name');

        $this->assertTrue(
            $template->isEqual( $template->getName() ),
            "Шаблон должен совпадать по своим параметрам"
        );
    }

    public function testIsNotEquals()
    {
        $template = new Theme\Template();
        $template->setName('name');

        $this->assertFalse(
            $template->isEqual( uniqid("some") ),
            "Шаблон не должен совпадать по параметрам"
        );
    }

}
