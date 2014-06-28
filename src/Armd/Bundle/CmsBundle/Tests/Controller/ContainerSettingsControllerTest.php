<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */

namespace Armd\Bundle\CmsBundle\Tests\UsageType;

use PHPUnit_Framework_TestCase;

use Armd\Bundle\CmsBundle\UsageType\BaseUsageService;
use Armd\Bundle\CmsBundle\UsageType\Param\StringParam;
use Armd\Bundle\CmsBundle\Controller\ContainerSettingsController;
use Armd\Bundle\CmsBundle\Admin\BaseContainerAdmin;
use Armd\Bundle\CmsBundle\UsageType\Param\EntityParam;
use Armd\Bundle\CmsBundle\UsageType\UsageType;
use Armd\Bundle\CmsBundle\Model\Theme\Template;

use Sonata\AdminBundle\Security\Handler\NoopSecurityHandler;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ContainerSettingsControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Armd\Bundle\CmsBundle\Controller\ContainerSettingsController
     */
    protected $containerSettingsController;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    public function testUsageTypeAction()
    {
        $this->setRequest(array('usageServiceName' => 'some'));
        $this->setContainer();
        $this->setContainerUsageType();

        $translator = $this->getMockBuilder('Symfony\\Component\\Translation\\TranslatorInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $translator->expects($this->any())->method('trans')->will($this->returnValue('translateTitle'));

        $this->container->expects($this->at(5))->method('get')->will($this->returnValue($translator));

        $this->setContainerSettingsController();

        $result = $this->containerSettingsController->usageTypeAction();

        $this->assertNotNull($result->getContent(), "Не найдено ни одного представления, пустой результат");

        $decodeResult = json_decode($result->getContent(), true);

        $this->ArrayHasKey('choise_list', $decodeResult['data'], "Не содержит представлений");

        $this->assertCount(3, $decodeResult['data']['choise_list'], "Число представлений не соответствует заданному");

        foreach ( $decodeResult['data']['choise_list'] as $usageType ) {
            $this->assertEquals($usageType, 'translateTitle', 'Название представления не переведенно');
        }

    }

    public function testUsageTypeParamsAction()
    {
        $this->setRequest(array('usageServiceName' => 'some', 'usageTypeName' => 'listItem'));
        $this->setContainer();
        $this->setContainerUsageType();

        $this->setContainerDoctrine();
        $this->setContainerSettingsController();

        $result = $this->containerSettingsController->usageTypeParamsAction();

        $this->assertNotNull($result->getContent(), "Не найдено ни одного параметра, пустой результат");

        $decodeResult = json_decode($result->getContent(), true);

        $this->assertCount(3, $decodeResult['data'], "Число параметров не соответствует заданному");

        foreach ( $decodeResult['data'] as $key => $param ) {
            $this->assertNotEmpty($param, 'Параметр '. $key .' не содержит данных ');
            $this->ArrayHasKey('name', $param, 'Парамет не содержит поля name');
            $this->ArrayHasKey('title', $param, 'Парамет не содержит поля title');
            $this->ArrayHasKey('defaultValue', $param, 'Парамет не содержит поля defaultValue');
            $this->ArrayHasKey('requirements', $param, 'Парамет не содержит поля requirements');
        }
    }

    public function testSetResponse()
    {
        $this->setRequest();
        $this->setContainer();

        $this->setContainerSettingsController();

        $result = $this->containerSettingsController->setResponse(array());

        $response = new Response();
        $this->assertNotSame($response, $result, 'Неверный тип возвращаемого результата');

        $this->assertEquals(200, $result->getStatusCode(), 'Неверный код результата');

        $this->assertEquals(array('data' => null, 'status' => 200, 'error' => ''),
                            json_decode($result->getContent(), true),
                            'Не верный контент возвращаемого результата');
    }

    public function testSetErrorResponse()
    {
        $this->setRequest();
        $this->setContainer();

        $this->setContainerSettingsController();

        $result = $this->containerSettingsController->setExeptionResponse();

        $response = new Response();
        $this->assertNotSame($response, $result, 'Неверный тип возвращаемого результата');

        $this->assertEquals(403, $result->getStatusCode(), 'Неверный код результата');
    }

    public function testSetParamData()
    {
        $this->setRequest();
        $this->setContainer();

        $this->setContainerDoctrine();
        $this->setContainerSettingsController();

        $this->containerSettingsController->setEntityManager();

        $entityParam = new EntityParam();
        $entityParam->setTitle('entityParam');
        $entityParam->setEntity('Entity');
        $entityParam->setAddField('EntityAddField');
        $entityParam->getViewField('EntityViewField');

        $usageType = new UsageType();
        $usageType->setName('someType');

        $settings = array('entityParam' => 12);

        $result = $this->containerSettingsController->setParamData($entityParam, $settings, $usageType, $this->getUsageService());

        $this->assertCount(8, $result, "Не верное число атрибутов");

        $this->assertEquals('entityParam', $result['title'], 'Не верное значение атрибута entityParam');
        $this->assertEquals('entity', $result['type'], 'Не верное значение атрибута entity');
        $this->assertEquals('someType', $result['usageTypeName'], 'Не верное значение атрибута entity');
        $this->assertEquals('some', $result['usageServiceName'], 'Не верное значение атрибута entity');
        $this->assertEquals(array(), $result['choise'], 'Не верное значение атрибута choise');
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

        $admin = new BaseContainerAdmin('armd_cms.admin.container', 'Armd\Bundle\CmsBundle\Entity\Container', 'ArmdCmsBundle:ContainerSettings', $this->container);
        $securityHandlerInterface = new NoopSecurityHandler();
        $admin->setSecurityHandler($securityHandlerInterface);

        $pool->expects($this->any())->method('getAdminByAdminCode')->will($this->returnValue($admin));

        $this->container->expects($this->at(0))->method('get')->will($this->returnValue($this->request));
        $this->container->expects($this->at(1))->method('get')->will($this->returnValue($pool));
        $this->container->expects($this->at(2))->method('get')->will($this->returnValue($this->request));

    }

    public function setContainerUsageType()
    {
        $pageManger = $this->getMockBuilder('Armd\Bundle\\CmsBundle\\Manager\\Page')
            ->setMethods(array('getTempalteService', 'getUsageService'))
            ->disableOriginalConstructor()
            ->getMock();
        $pageManger->expects($this->any())->method('getUsageService')->will($this->returnValue($this->getUsageService()));

        $themeService = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Services\\TemplateService')
            ->disableOriginalConstructor()
            ->getMock();

        $bundleTemplate = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\Model\\Theme\\BundleTemplate')
            ->disableOriginalConstructor()
            ->getMock();
        $template = new Template();
        $template->setName('template');
        $template->setTitle('title');

        $bundleTemplate->expects($this->any())->method('getTemplates')->will($this->returnValue(array($template)));

        $theme = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Model\\Theme\\Theme')
            ->disableOriginalConstructor()
            ->getMock();
        $theme->expects($this->any())->method('getBundleTemplate')->will($this->returnValue($bundleTemplate));

        $themeService->expects($this->any())->method('getTheme')->will($this->returnValue($theme));

        $pageManger->expects($this->any())->method('getTemplateService')->will($this->returnValue($themeService));

        $this->container->expects($this->at(3))->method('get')->will($this->returnValue($this->request));
        $this->container->expects($this->at(4))->method('get')->will($this->returnValue($pageManger));
    }

    public function setContainerDoctrine()
    {
        $entityRepository = $this->getMockBuilder('Doctrine\\ORM\\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $entityManager = $this->getMockBuilder('Doctrine\\ORM\\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->any())->method('getRepository')->will($this->returnValue($entityRepository));

        $doctrine = $this->getMockBuilder('Symfony\\Bundle\\DoctrineBundle\\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $doctrine->expects($this->any())->method('getEntityManager')->will($this->returnValue($entityManager));

        $this->container->expects($this->once())->method('has')->will($this->returnValue(true));
        $this->container->expects($this->any())->method('get')->will($this->returnValue($doctrine));
    }

    public function setContainerSettingsController()
    {
        $this->containerSettingsController = new ContainerSettingsController();

        $this->containerSettingsController->setContainer($this->container);
    }

    public function getUsageService()
    {
        $types = array('item' => array('controller' => 'someController',
                                       'action' => 'some_action',
                                       'title' => 'some_title',
                                       'params' => array('text_field' => array('title' => 'title',
                                                                               'type' => 'string',
                                                                               'requirements' => 1))),
                       'list' => array('controller' => 'someController',
                                       'action' => 'some_action',
                                       'title' => 'some_title',
                                       'params' => array('per_page' => array('title' => 'per_page',
                                                                             'type' => 'int',
                                                                             'default' => 10))));

        $group = array('listItem' => array('types' => array('item', 'list')),
                       'list'     => array('types' => array('list')),
                       'item'     => array('types' => array('item')));
        return new BaseUsageService('some', array('types' => $types, 'group' => $group), 'bundleName');
    }
}