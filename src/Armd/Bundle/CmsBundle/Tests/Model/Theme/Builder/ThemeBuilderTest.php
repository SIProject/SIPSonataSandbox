<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Tests\Model\Theme\Builder;

use Armd\Bundle\CmsBundle\Model\Theme\Builder\ThemeBuilder;
use Armd\Bundle\CmsBundle\Model\Theme\BundleTemplate;
use Armd\Bundle\CmsBundle\Model\Theme\Layout;

use PHPUnit_Framework_TestCase;

class ThemeBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Armd\Bundle\CmsBundle\Model\Theme\Builder\ThemeBuilder
     */
    protected $builder;

    /**
     * @var \Armd\Bundle\CmsBundle\Model\Theme\Builder\BundleTemplateBuilder
     */
    protected $bundleTemplateBuilder;

    /**
     * @var \Armd\Bundle\CmsBundle\Model\Theme\Builder\LayoutBuilder
     */
    protected $layoutBuilder;

    public function setUp()
    {
        parent::setUp();
        $this->builder = new ThemeBuilder();
        $this->layoutBuilder = $this->getMockBuilder('Armd\Bundle\CmsBundle\Model\Theme\Builder\LayoutBuilder')
            ->getMock();
        $this->builder->setLayoutBuilder($this->layoutBuilder);
        $this->bundleTemplateBuilder = $this->getMockBuilder('Armd\Bundle\CmsBundle\Model\Theme\Builder\BundleTemplateBuilder')
            ->getMock();
        $this->builder->setBundleTemplateBuilder($this->bundleTemplateBuilder);
    }

    public function testBuild()
    {
        $definition = array(
            'layout' => array('name' => array('def')),
            'module' => array('def' =>
                                array('template' =>
                                    array('demo' =>
                                        array('controller' => 'demo',
                                              'action' => 'item')
                                    )
                                )
                             ),
            'title' => 'тестовая',
        );

        $layout = new Layout();
        $layoutName = uniqid();
        $layout->setName($layoutName);
        $this->layoutBuilder
            ->expects($this->once())
            ->method('build')
            ->with($this->equalTo('name'), $this->equalTo(array('def')))
            ->will($this->returnValue($layout));

        $bundleTemplate = new BundleTemplate();
        $this->bundleTemplateBuilder->expects($this->any())->method('build')->will($this->returnValue($bundleTemplate));

        $theme = $this->builder->build('testName', $definition);

        $this->assertInstanceOf('Armd\Bundle\CmsBundle\Model\Theme\Theme', $theme);
        $this->assertEquals('testName', $theme->getName(), "Имя темы должно совпадать");
        $this->assertEquals('тестовая', $theme->getTitle(), "Название темы должно совпадать");
        $this->assertEquals($layout, $theme->getLayout($layoutName), "Тема должна содержать Layout");
    }

    public function testValidate()
    {
        $definition = array(
            'title' => 'тестовая',
            'layout' => array('name' => array('def')),
            'module' => array('name' => array('def')),
        );

        $this->layoutBuilder
            ->expects($this->once())
            ->method('validate')
            ->with($this->equalTo('name'), $this->equalTo(array('def')))
            ->will($this->returnValue(true));

        $this->bundleTemplateBuilder
            ->expects($this->once())
            ->method('validate')
            ->with($this->equalTo('name'), $this->equalTo(array('def')))
            ->will($this->returnValue(true));

        $this->builder->validate('some', $definition);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testValidateWithoutTitle()
    {
        $definition = array();
        $this->builder->validate('some', $definition);
    }

}
