<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */

namespace Armd\ContentAbstractBundle\Tests\Controller;

use PHPUnit_Framework_TestCase;

use Armd\ContentAbstractBundle\Controller\Controller;
use Armd\Bundle\CmsBundle\Model\Theme\Layout;
use Armd\Bundle\CmsBundle\UsageType\UsageType;
use Armd\Bundle\CmsBundle\UsageType\Param\IntParam;

class ControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Armd\Bundle\CmsBundle\Controller\Controller
     */
    protected $controller;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function setUp()
    {
        $this->controller = new Controller();

        $pageManager = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Manager\\Page')->disableOriginalConstructor()->getMock();

        $entityPageManager = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\Entity\\PageManager')->disableOriginalConstructor()->getMock();

        $entityPageManager->expects($this->any())->method('getThemeName')
            ->will($this->returnValue('ThemeName'));

        $entityPageManager->expects($this->any())->method('getDefaultThemeName')
            ->will($this->returnValue('DefaultThemeName'));

        $pageManager->expects($this->any())->method('getPageManager')
            ->will($this->returnValue($entityPageManager));

        $this->controller->init( new Layout(),
                                 $this->getUsageType(),
                                 $pageManager,
                                 $this->getMockBuilder('Symfony\\Component\\Templating\\EngineInterface')->disableOriginalConstructor()->getMock());
    }

    /**
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageType
     */
    public function getUsageType()
    {
        $usageType = new UsageType();

        $usageType->setController('TestController');
        $usageType->setAction('TestAction');
        $usageType->setBundleName('BundleName');
        
        return $usageType;
    }

    public  function testGetEntityName()
    {
        $result = $this->controller->getEntityName();

        $this->assertEquals('BundleName:TestController', $result, 'Не верное имя Entity');
    }

    public function testGetCommonTemplate()
    {
        $result = $this->controller->getCommonTemplate('testTemplate.html.twig');

        $this->assertEquals('DefaultThemeName:common:testTemplate.html.twig', $result, 'Не верной путь к шаблону');
    }
}