<?php
/*
 * (c) Sukhinin Ilja <isuhinin@armd.ru>
 */

namespace Armd\Bundle\CmsBundle\Tests\Manager;

use PHPUnit_Framework_TestCase;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Routing\Route;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Response;

use Armd\Bundle\CmsBundle\Manager\PageManager;
use Armd\Bundle\CmsBundle\Entity\Page;
use Armd\Bundle\CmsBundle\Entity\Container;
use Armd\Bundle\CmsBundle\Entity\PageType;
use Armd\Bundle\CmsBundle\UsageType\BaseUsageService;

class PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    protected $pageManager;

    /**
     * @var \Armd\Bundle\CmsBundle\Entity\PageManager
     */
    protected $entitypageManager;

    /**
     * @var \Armd\Bundle\CmsBundle\Entity\PageContainer
     */
    protected $pageContainerManager;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    protected $TimeCalled = 0;

    /**
     * We'll create some simple mocks for every test to configure
     *
     * @return void
     */
    protected function setUp()
    {
        $this->container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entitypageManager = $this->getMockBuilder('Armd\\Bundle\CmsBundle\\Entity\\PageManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pageContainerManager = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Entity\\PageContainerManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pageManager = $this->getPageManager();
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    public function getPageManager()
    {
        return new PageManager($this->container, $this->entitypageManager);
    }

    public function setContainer()
    {
        $response = $this->getMockBuilder('Symfony\\Component\\HttpFoundation\\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $containerService = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Services\\ContainerService')
            ->disableOriginalConstructor()
            ->getMock();

        $containerService->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($response));

        $this->container->expects($this->any())
            ->method('get')
            ->will($this->returnValue($containerService));
    }

    public function getPageFixture()
    {
        $pageType = new PageType();

        $page = new EntityPage();
        $page->setId(1);

        $containers[0] = new Container();
        $containers[0]->setIsMain(false);
        $containers[0]->setName('Content_header');
        $pageType->addContainer($containers[0]);

        $containers[1] = new Container();
        $containers[1]->setIsMain(true);
        $containers[1]->setName('Content');
        $pageType->addContainer($containers[1]);

        $containers[2] = new Container();
        $containers[2]->setIsMain(false);
        $containers[2]->setName('Content_footer');
        $pageType->addContainer($containers[2]);

        $page->setPageType($pageType);

        return $page;
    }

    public function testRenderPageContainers()
    {
        $this->setContainer();
        $this->pageManager = $this->getPageManager();

        $page = $this->getPageFixture();
        $this->pageManager->setCurrentPage($page);

        $containerCollection = $page->getPageType()->getContainers();

        $result = $this->pageManager->renderPageContainers($containerCollection, $page);

        $this->assertNotEmpty($result, "В результате нет ни одного блока");
        $this->assertCount(3, $result, "Число контейнеров в ContainerCollection не совпадает с числом контейнеров в PageType");
    }

    public function testRenderPageContainersMain()
    {
        $this->setContainer();
        $this->pageManager = $this->getPageManager();

        $page = $this->getPageFixture();
        $this->pageManager->setCurrentPage($page);

        $containerCollection = $page->getPageType()->getContainers();

        $result = $this->pageManager->renderPageContainers($containerCollection, $page);

        foreach ($result as $key => $container) {
            $this->assertEquals($key, 'Content', "Первым должен быть обработан главный блок");
            break;
        }
    }

    public function testRenderPageContainersCallRenderContainer()
    {
        $page = $this->getPageFixture();
        $containerCollection = $page->getPageType()->getContainers();

        $pageManager = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Manager\\Page')
            ->setMethods(array('renderContainer'))
            ->disableOriginalConstructor()
            ->getMock();

        $pageManager->setCurrentPage($page);

        $pageManager->expects($this->exactly(3))
                    ->method('renderContainer')
                    ->with($this->logicalOr($this->equalTo('Content'),
                                            $this->equalTo('Content_header'),
                                            $this->equalTo('Content_footer')))
                    ->will($this->returnValue(null));

        $pageManager->renderPageContainers($containerCollection, $page);
    }

    public function testResponseContainer()
    {
        $container = new Container();
        $container->setIsMain(true);
        $container->setName('Content');

        $pageManager = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Manager\\Page')
            ->setMethods(array('getBlockContainer'))
            ->disableOriginalConstructor()
            ->getMock();

        $containerService = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Services\\ContainerService')
            ->disableOriginalConstructor()
            ->getMock();

        $containerService->expects($this->once())
                    ->method('execute')
                    ->with($this->equalTo($pageManager),
                           $this->equalTo('Content'))
                    ->will($this->returnValue(new Response()));

        $pageManager->expects($this->any())
            ->method('getBlockContainer')
            ->will($this->returnValue($containerService));

        $pageManager->responseContainer($container);
    }

    public function testRenderContainer()
    {
        $pageManager = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Manager\\Page')
            ->setMethods(array('findContainer', 'responseContainer'))
            ->disableOriginalConstructor()
            ->getMock();

        $pageManager->expects($this->any())
                    ->method('findContainer')
                    ->with($this->equalTo('Content'))
                    ->will($this->returnValue(new Container()));

        $pageManager->expects($this->any())
                    ->method('responseContainer')
                    ->with($this->equalTo(new Container()))
                    ->will($this->returnValue(new Response()));

        $pageManager->renderContainer('Content');
    }

    /**
     * @todo Необходимо доработать метод,
     * избавиться от конструкции try catch, так как сейчас из-за неё не работает тест
     */
    public function testGetUrlPath()
    {
        $pageManager = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Manager\\Page')
            ->setMethods(array('getUsageService', 'getRouter'))
            ->disableOriginalConstructor()
            ->getMock();

        $pageManager->expects($this->any())
                    ->method('getUsageService')
                    ->will($this->returnValue($this->getUsageService()));

        $router = $this->getMockBuilder('Symfony\\Component\\Routing\\Router')
            ->setMethods(array('generate'))
            ->disableOriginalConstructor()
            ->getMock();

        $router->expects($this->any())
                    ->method('generate')
                    ->with($this->equalTo('some.item.item'),
                           $this->equalTo(array()))
                    ->will($this->returnValue(new Route('/')));

        $pageManager->expects($this->any())
                    ->method('getRouter')
                    ->will($this->returnValue($router));

        $pageManager->getUrlPath('some', 'item', array());
    }

    public function getUsageService()
    {
        $types = array('item' => array('controller' => 'someController',
                                       'action' => 'some_action',
                                       'title' => 'some_title',
                                       'params' => array('text_field' => array('title' => 'title',
                                                                               'type' => 'string',
                                                                               'requirements' => 1))));

        $group = array('item'     => array('types' => array('item')));
        return new BaseUsageService('some', array('types' => $types, 'group' => $group), 'bundleName');
    }
}