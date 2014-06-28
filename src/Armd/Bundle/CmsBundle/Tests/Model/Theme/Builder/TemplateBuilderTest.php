<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Tests\Model\Theme\Builder;

use Armd\Bundle\CmsBundle\Model\Theme\Builder\TemplateBuilder;
use Armd\Bundle\CmsBundle\Model\Theme\Template;

use PHPUnit_Framework_TestCase;

class TemplateBuilderTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var TemplateBuilder
     */
    protected $builder;

    public function setUp()
    {
        parent::setUp();
        $this->builder = new TemplateBuilder();
    }


    public function testBuild()
    {
        $definition = array(
            'action' => 'someAction',
            'title' => 'someTitle',
            'controller' => 'someController'
        );

        $template = $this->builder->build('testName', $definition);
        $this->assertInstanceOf('Armd\Bundle\CmsBundle\Model\Theme\Template', $template);
        $this->assertEquals('testName', $template->getName(), "Имя шаблона должно совпадать");
        $this->assertEquals('someTitle', $template->getTitle(), "Имя шаблона должно совпадать");
    }

    public function testValidate()
    {
        $definition = array(
            'action' => 'someAction',
            'title' => 'someTitle',
            'controller' => 'someController'
        );

        $this->builder->validate('some', $definition);
    }

}
