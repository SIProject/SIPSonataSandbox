<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Tests\Model\Theme;

use PHPUnit_Framework_TestCase;

use Armd\Bundle\CmsBundle\Model\Theme;

class ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Armd\Bundle\CmsBundle\Model\Theme\Theme
     */
    protected $baseTheme;

    /**
     * @var \Armd\Bundle\CmsBundle\Model\Theme\Layout
     */
    protected $baseLayout;

    public function setUp() {
        parent::setUp();
        $this->baseTheme = new Theme\Theme();
        $this->baseLayout = new Theme\Layout();
        $this->baseLayout->setName('MainLayout');
        $this->baseTheme->addLayout($this->baseLayout);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetUnexistBundleTemplate()
    {
        $this->baseTheme->getBundleTemplate('Name');
    }


    public function testMatchTemplateWithAdditionalNameExist()
    {
        $controller = 'SomeController';
        $action = 'SomeAction';
        $tName = 'somename';

        $template = new Theme\Template();

        $bundleTemplate = $this->getMockBuilder('Armd\Bundle\CmsBundle\Model\Theme\BundleTemplate')
                ->getMock();

        $bundleTemplateSecond = $this->getMockBuilder('Armd\Bundle\CmsBundle\Model\Theme\BundleTemplate')
                ->getMock();

        $bundleTemplate
            ->expects($this->once())
            ->method('matchTemplateStrictName')
            ->with( $this->equalTo( $controller), $this->equalTo($action), $this->equalTo($tName) )
            ->will( $this->returnValue( $template ) );

        $this->assertInstanceOf('Armd\Bundle\CmsBundle\Model\Theme\BundleTemplate', $bundleTemplate);


        $result = $this->baseTheme->matchTemplate(
            array($bundleTemplate, $bundleTemplateSecond),
            $controller,
            $action,
            $tName
        );

        $this->assertEquals($result, $template, "Должен вернуть экземпляр шаблона");
    }

    /**
     * Проверяем, что для шаблонов, не имеющих дополнительный action-шаблон, возвращается шаблон по умолчанию
     */
    public function testMatchTemplateWithAdditionalNameNonExist()
    {
        $controller = 'SomeController';
        $action = 'SomeAction';
        $tName = 'somename';

        $template = new Theme\Template();
        $template->setName(uniqid());

        $bundleTemplate = $this->getMockBuilder('Armd\Bundle\CmsBundle\Model\Theme\BundleTemplate')
                ->getMock();

        $bundleTemplateSecond = $this->getMockBuilder('Armd\Bundle\CmsBundle\Model\Theme\BundleTemplate')
                ->getMock();

        //должен для 1го бандла найти шаблон по умолчанию, для заданного экшена
        $bundleTemplate
            ->expects($this->once())
            ->method('matchTemplate')
            ->with( $this->equalTo( $controller), $this->equalTo($action) )
            ->will( $this->returnValue( $template ) )
        ;

        $result = $this->baseTheme->matchTemplate(
            array($bundleTemplate, $bundleTemplateSecond),
            $controller,
            $action,
            $tName
        );

        $this->assertEquals($result, $template, "Должен вернуть экземпляр шаблона");
    }

}
