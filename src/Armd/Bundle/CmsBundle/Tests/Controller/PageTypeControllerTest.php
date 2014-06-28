<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */

namespace Armd\Bundle\CmsBundle\Tests\Controller;

use PHPUnit_Framework_TestCase;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Armd\Bundle\CmsBundle\Admin\PageTypeAdmin;
use Armd\Bundle\CmsBundle\Controller\PageTypeController;
use Armd\Bundle\CmsBundle\Entity\PageType;
use Armd\Bundle\CmsBundle\Entity\Container;

use Sonata\AdminBundle\Security\Handler\NoopSecurityHandler;
use Sonata\AdminBundle\Admin\Pool;

class PageTypeControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Armd\Bundle\CmsBundle\Controller\PageTypeController
     */
    protected $controller;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function setUp()
    {
        $this->setRequest();
        $this->setContainer();

        $this->setController();
    }

    public function testGetContainers()
    {
        $translator = $this->getMockBuilder('Symfony\\Component\\Translation\\TranslatorInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $translator->expects($this->any())->method('trans')->will($this->returnValue('transText'));

        $containerAdmin = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Admin\\ContainerAdmin')
            ->disableOriginalConstructor()
            ->getMock();
        $containerAdmin->expects($this->any())->method('generateObjectUrl')->will($this->returnValue('url'));

        $this->container->expects($this->at(0))->method('get')->will($this->returnValue($translator));
        $this->container->expects($this->any())->method('get')->will($this->returnValue($containerAdmin));

        $result = $this->controller->getContainers($this->setPageType());

        $this->assertCount(2, $result, 'Не соответствует число блоков');

        $this->assertArrayHasKey("Container 2", $result, 'Нет блока "Container 2"');
        $this->assertArrayHasKey("Container 1", $result, 'Нет блока "Container 1"');

        $this->assertEquals('transText', $result['Container 1']['usageType'], 'Поля блока не переведенны');
        $this->assertEquals('url', $result['Container 1']['url'], 'Не верное поле url');
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Entity\PageType
     */
    public function setPageType()
    {
        $pageType = new PageType();
        $container = new Container();
        $container->setName('Container 1');
        $container->setUsageType('UsageType');
        $container->setUsageService('UsageService');
        $pageType->addContainer($container);

        $container = new Container();
        $container->setName('Container 2');
        $container->setUsageType('UsageType 1');
        $container->setUsageService('UsageService 1');
        $pageType->addContainer($container);

        return $pageType;
    }

    /**
     * @param array $params
     */
    public function setRequest(array $params = array())
    {
        $requestParams = array_merge(array('_sonata_admin' => 'armd_cms.admin.container'), $params);

        $this->request = new Request($requestParams);
    }

    public function setContainer()
    {
        $this->container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $pool = $this->getMockBuilder('Sonata\\AdminBundle\\Admin\\Pool')
            ->disableOriginalConstructor()
            ->getMock();

        $admin = new PageTypeAdmin('armd_cms.admin.container', 'Armd\Bundle\CmsBundle\Entity\Container', 'ArmdCmsBundle:ContainerSettings', $this->container);
        $securityHandlerInterface = new NoopSecurityHandler();
        $admin->setSecurityHandler($securityHandlerInterface);

        $pool->expects($this->any())->method('getAdminByAdminCode')->will($this->returnValue($admin));

        $this->container->expects($this->at(0))->method('get')->will($this->returnValue($this->request));
        $this->container->expects($this->at(1))->method('get')->will($this->returnValue($pool));
        $this->container->expects($this->at(2))->method('get')->will($this->returnValue($this->request));
    }

    public function setController()
    {
        $this->controller = new PageTypeController();

        $this->controller->setContainer($this->container);
    }
}