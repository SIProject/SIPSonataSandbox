<?php

namespace {{ namespace }}\Tests\Controller;

use Armd\Bundle\CmsBundle\UsageType\UsageType;
use Armd\Bundle\CmsBundle\Model\Theme\Layout;

use PHPUnit_Framework_TestCase;

class {{ name }}ControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \{{ namespace }}\Controller\{{ name }}Controller
     */
    protected $controller;

    public function setUp()
    {
        $this->setContainer();

        $this->controller = $this->getMockBuilder('Armd\\TestBundle\\Controller\\TestController')
             ->setMethods(array('renderCms'))
             ->disableOriginalConstructor()
             ->getMock();

        $this->controller->init(new Layout(),
                                new UsageType(),
                                $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Manager\\Page')->disableOriginalConstructor()->getMock(),
                                $this->getMockBuilder('Symfony\\Component\\Templating\\EngineInterface')->disableOriginalConstructor()->getMock());
    }

    public function testIndexAction()
    {
        $this->controller->expects($this->any())->method('renderCms')
            ->with($this->equalTo(array('message' => 'Simple bundle')))
            ->will($this->returnValue(true));

        $this->controller->indexAction(new UsageType(), 'Simple bundle');
    }

    public function setContainer()
    {
        $this->container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
