<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Tests\Model\Theme;

use PHPUnit_Framework_TestCase;

use Armd\Bundle\CmsBundle\Model\Theme;

class BundleTemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Armd\Bundle\CmsBundle\Model\Theme\Template
     */
    protected $templateExpect;

    public function setUp()
    {
        parent::setUp();
        $this->templateExpect = new Theme\Template();
        $this->templateExpect->setName( 'list' );
    }
    
    public function testMatchTemplateStrictName()
    {
        $templateAnother = clone $this->templateExpect;
        $templateAnother->setName( uniqid('some') );

        $bundleTemplate = new Theme\BundleTemplate();
        $bundleTemplate->addTemplate($templateAnother);
        $bundleTemplate->addTemplate($this->templateExpect);

        $matchedTemplate = $bundleTemplate->matchTemplateStrictName('Controller', 'Action', 'list');

        $this->assertNotNull($matchedTemplate, "Должен вернуть объект");
        $this->assertEquals($this->templateExpect, $matchedTemplate, "Должен вернуть шаблон другого типа");
    }

    public function testMatchTemplate()
    {
        $this->templateExpect->setName( 'controller' );

        $templateAnother = clone $this->templateExpect;
        $templateAnother->setName( uniqid('some') );

        $bundleTemplate = new Theme\BundleTemplate();
        $bundleTemplate->addTemplate($templateAnother);
        $bundleTemplate->addTemplate($this->templateExpect);

        $matchedTemplate = $bundleTemplate->matchTemplate('Controller', 'Action');

        $this->assertNotNull($matchedTemplate, "Должен вернуть объект");
        $this->assertEquals($this->templateExpect, $matchedTemplate, "Должен вернуть шаблон другого типа");
    }

}
