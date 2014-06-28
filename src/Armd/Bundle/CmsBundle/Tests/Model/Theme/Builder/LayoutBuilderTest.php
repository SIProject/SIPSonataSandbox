<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Tests\Model\Theme\Builder;

use Armd\Bundle\CmsBundle\Model\Theme\Builder\LayoutBuilder;
use Armd\Bundle\CmsBundle\Model\Theme\BundleTemplate;

use PHPUnit_Framework_TestCase;

class LayoutBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Armd\Bundle\CmsBundle\Model\Theme\Builder\LayoutBuilder
     */
    protected $builder;

    /**
     * @var \Armd\Bundle\CmsBundle\Model\Theme\Builder\BundleTemplateBuilder
     */
    protected $bundleTemplateBuilder;

    public function setUp()
    {
        parent::setUp();
        $this->builder = new LayoutBuilder();
        $this->bundleTemplateBuilder = $this->getMockBuilder('Armd\Bundle\CmsBundle\Model\Theme\Builder\BundleTemplateBuilder')
            ->getMock();
        $this->builder->setBundleTemplateBuilder($this->bundleTemplateBuilder);
    }

    public function testBuild()
    {
        $definition = array(
            'title' => 'тестовая',
            'default' => true,
            'module' => array('name' => array('def'))
        );

        $bundleTemplate = new BundleTemplate();
        $bundleTemplate->setName('some');

        $this->bundleTemplateBuilder
            ->expects($this->once())
            ->method('build')
            ->with($this->equalTo('name'), $this->equalTo(array('def')))
            ->will($this->returnValue($bundleTemplate));

        $layout = $this->builder->build('testName', $definition);
        $this->assertInstanceOf('Armd\Bundle\CmsBundle\Model\Theme\Layout', $layout);
        $this->assertEquals('testName', $layout->getName(), "Имя темы должно совпадать");
        $this->assertEquals('тестовая', $layout->getTitle(), "Название темы должно совпадать");
        $this->assertTrue($layout->isDefault(), "Должна быть отмечена как default");
    }

    public function testValidate()
    {
        $definition = array(
            'title' => 'тестовая',
            'module' => array('name' => array('def'))
        );

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
