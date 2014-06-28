<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */

namespace Armd\Bundle\CmsBundle\Tests\Controller;

use PHPUnit_Framework_TestCase;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Armd\Bundle\CmsBundle\Admin\PageTypeAdmin;
use Armd\Bundle\CmsBundle\Entity\PageContainer;

use Sonata\AdminBundle\Security\Handler\NoopSecurityHandler;
use Sonata\AdminBundle\Admin\Pool;

class BlockControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Armd\Bundle\CmsBundle\Controller\BaseResponseController
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

    /**
     * @var \Sonata\AdminBundle\Admin\Pool
     */
    protected $pool;

    /**
     * @var \Armd\Bundle\CmsBundle\Admin\PageTypeAdmin
     */
    protected $admin;

    public function setUp()
    {
        $this->setRequest(array('source' => 1, 'target' => 2));
        $this->controller = $this->getMockForAbstractClass('Armd\\Bundle\\CmsBundle\\Controller\\BlocksController');

        $this->setContainer();
        $this->controller->setContainer($this->container);
    }

    public function setContainer()
    {
        $this->container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pool = new Pool($this->container, 'title', 'title');

        $this->pool = $this->getMockBuilder('Sonata\\AdminBundle\\Admin\\Pool')
            ->disableOriginalConstructor()
            ->getMock();

        $this->admin = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Admin\\PageTypeAdmin')
            ->setConstructorArgs(array('armd_cms.admin.pagetype', 'Armd\Bundle\CmsBundle\Entity\PageType', 'ArmdCmsBundle:ContainerSettings', $this->container))
            ->getMock();

        $securityHandlerInterface = new NoopSecurityHandler();
        $this->admin->setSecurityHandler($securityHandlerInterface);

        $this->pool->expects($this->any())->method('getAdminByAdminCode')->will($this->returnValue($this->admin));

        $this->container->expects($this->at(0))->method('get')->will($this->returnValue($this->request));
        $this->container->expects($this->at(1))->method('get')->will($this->returnValue($this->pool));
        $this->container->expects($this->at(2))->method('get')->will($this->returnValue($this->request));
    }

    /**
     * @param array $params
     */
    public function setRequest(array $params = array())
    {
        $requestParams = array_merge(array('_sonata_admin' => 'armd_cms.admin.pagetype'), $params);

        $this->request = new Request($requestParams);
    }

    public function testRender()
    {
        $template = $this->getMockBuilder('Symfony\\Bundle\\TwigBundle\\TwigEngine')
            ->setMethods(array('renderResponse', 'exists'))
            ->disableOriginalConstructor()
            ->getMock();
        $template->expects($this->any())->method('exists')->will($this->returnValue(true));
        $template->expects($this->any())->method('renderResponse')
                 ->with($this->anything(),
                        $this->logicalAnd($this->arrayHasKey('blockPath'),
                                          $this->arrayHasKey('containers')),
                        $this->anything())
                 ->will($this->returnValue(true));

        $this->container->expects($this->any())->method('get')->will($this->returnValue($template));

        $form = $this->getMockBuilder('Symfony\\Component\\Form\FormView')
            ->disableOriginalConstructor()
            ->getMock();

        $this->controller->render('Template', array('action' => 'edit', 'form' => $form, 'base_template' => ''));
    }

    public function  testCopyBlockAction()
    {
        $this->container->expects($this->any())
                        ->method('get')
                        ->will($this->returnValue($this->request));

        $this->controller->expects($this->any())
                         ->method('getAdmin')
                         ->will($this->returnValue($this->admin));

        $result = $this->controller->copyBlockAction();

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\Response', $result, 'Не верный тип возвращаемого значения');

        $this->assertEquals(array('status' => 403, 'error' => 'Source can not be null!'), json_decode($result->getContent(), 'Не верный результат'));

        $this->assertEquals(403, $result->getStatusCode(), 'Не верный код результата');

    }

    public function testSetTargetObject()
    {
        $sourceObject = new PageContainer();
        $sourceObject->setSettings(array('UsageService:UsageType' => array('parametrs' => array('settings'))));
        $sourceObject->setUsageService('UsageService');
        $sourceObject->setUsageType('UsageType');

        $targetObject = new PageContainer();
        /**
         * @var PageContainer $result
         */
        $result = $this->controller->setTargetObject($targetObject, $sourceObject);

        $this->assertEquals('UsageService', $result->getUsageService(), 'Не соответствует UsageService');

        $this->assertEquals('UsageType', $result->getUsageType(), 'Не соответствует UsageType');

        $this->assertEquals(array('UsageService' => array('UsageType' => array('parametrs' => array('settings')))), $result->getSettings(), 'Не соответствует UsageSettings');
    }
}