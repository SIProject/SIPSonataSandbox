<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Tests\Model\Theme\Builder;

use Armd\Bundle\CmsBundle\Model\Theme\Builder\BundleTemplateBuilder;
use Armd\Bundle\CmsBundle\Model\Theme\Builder\TemplateBuilder;
use Armd\Bundle\CmsBundle\Model\Theme\Template;

use PHPUnit_Framework_TestCase;

class BundleTemplateBuilderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var BundleTemplateBuilder
     */
    protected $builder;

    /**
     * @var TemplateBuilder
     */
    protected $templateBuilder;

    public function setUp()
    {
        parent::setUp();
        $this->builder = new BundleTemplateBuilder();
        $this->templateBuilder = $this->getMockBuilder('Armd\Bundle\CmsBundle\Model\Theme\Builder\TemplateBuilder')
            ->getMock();
        $this->builder->setTemplateBuilder($this->templateBuilder);
    }

    public function testBuild()
    {
        $definition = array(
            'template' => array(
                'name' => array('def')
            )
        );

        $template = new Template();
        $template->setName(uniqid());

        $this->templateBuilder
            ->expects($this->once())
            ->method('build')
            ->with($this->equalTo('name'), $this->equalTo($definition['template']['name']))
            ->will($this->returnValue($template));

        $bundleTemplate = $this->builder->build('testName', $definition);
        $this->assertInstanceOf('Armd\Bundle\CmsBundle\Model\Theme\BundleTemplate', $bundleTemplate);
        $this->assertEquals('testName', $bundleTemplate->getName(), "Имя модуля должно совпадать");
        $this->assertEquals(array($template), $bundleTemplate->getTemplates(), "Модуль должен содержать шаблон");
    }

    public function testValidate()
    {
        $definition = array(
            'template' => array(
                'name' => array('def')
            )
        );

        $this->templateBuilder
            ->expects($this->once())
            ->method('validate')
            ->with($this->equalTo('name'), $this->equalTo(array('def')))
            ->will($this->returnValue(true));

        $this->builder->validate('some', $definition);
    }
}
