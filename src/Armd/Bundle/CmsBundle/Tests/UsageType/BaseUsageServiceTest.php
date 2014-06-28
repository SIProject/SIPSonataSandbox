<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */

namespace Armd\Bundle\CmsBundle\Tests\UsageType;

use PHPUnit_Framework_TestCase;

use Armd\Bundle\CmsBundle\UsageType\BaseUsageService;
use Armd\Bundle\CmsBundle\UsageType\UsageType;
use Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer;
use Armd\Bundle\CmsBundle\Entity\Container as PageTypeContainer;
use Armd\Bundle\CmsBundle\Entity\PageContainer;
use Armd\Bundle\CmsBundle\UsageType\Param\StringParam;
use Armd\Bundle\CmsBundle\UsageType\Param\TemplateParam;

class BaseUsageServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Параметры заданные в контейнерах, должны переопределяться
     */
    public function testParamsBuild() {
        $usageServiceName = uniqid('name');
        $usageTypeName = uniqid('name');

        $typeParams = array('value1' => uniqid(), 'value2' => true);
        $pageParams = array('value2' => uniqid());
        $pageTypeContainer = new PageTypeContainer();
        $pageTypeContainer->setUsageTypeSettings($usageTypeName, $usageServiceName, $typeParams);

        $pageContainer = new PageContainer();
        $pageContainer->setUsageTypeSettings($usageTypeName, $usageServiceName, $pageParams);

        //init usage type with params
        $usageType = new UsageType();
        $usageType->setName($usageTypeName);
        $param1 = new StringParam();
        $param1->setName('value1');
        $param1->setDefault( uniqid('default') );
        $param2 = new StringParam();
        $param2->setName('value2');
        $usageType->setParams( array( $param1, $param2) );

        $usageTypeContainer = new UsageTypeContainer();
        $usageTypeContainer->addType($usageType);

        $containerParam = new StringParam();
        $containerParam->setName('template');
        $usageTypeContainer->addParam($containerParam);

        //init expectedValues
        $expectedParam1 = clone $param1;
        $expectedParam2 = clone $param2;
        $expectedParam2->setValue($pageParams['value2']);
        $expectedParam1->setValue($typeParams['value1']);

        $service = new BaseUsageService('some', array('types' => array(), 'group' => array()), 'bundleName');
        $service->paramsBuild($usageType, $usageTypeContainer, $usageServiceName, $pageTypeContainer, $pageContainer);
        $result = $usageType->getParams();

        $this->assertInternalType('array', $result, 'Должен вернуть массив UsageType\Param\BaseParam');
        $this->assertCount(3, $result, 'Должен содержать 2 параметра');
        $this->assertEquals($expectedParam1, $result['value1'], '1-ий параметр не идентичен');
        $this->assertEquals($expectedParam2, $result['value2'], '2-ий параметр не идентичен');
        $this->assertEquals($containerParam, $result['template'], '3-ий параметр не идентичен');
    }

    public function testParamsDefaultValueForNonDefinedType()
    {
        $pageTypeContainer = new PageTypeContainer();

        $pageContainer = new PageContainer();

        //init usage type with params
        $usageType = new UsageType();
        $usageType->setName('notdefined');
        $param1 = new StringParam();
        $param1->setName('value1');
        $param1->setDefault( uniqid('default') );

        //init expectedValues
        $expectedParam1 = clone $param1;
        $usageType->setParams( array( $param1) );

        $usageTypeContainer = new UsageTypeContainer();
        $usageTypeContainer->addType($usageType);

        $service = new BaseUsageService('some', array('types' => array(), 'group' => array()), 'bundleName');
        $service->paramsBuild($usageType, $usageTypeContainer, 'some', $pageTypeContainer, $pageContainer);

        $result = $usageType->getParams();

        $this->assertInternalType('array', $result, 'Должен вернуть массив UsageType\Param\BaseParam');
        $this->assertCount(1, $result, 'Должен содержать 1 параметр');
        $this->assertEquals($expectedParam1, $result['value1'], 'Параметры не идентичены');
    }

    public function testParseUsageType()
    {
        $arrayDefention = array( 
            'controller'    => 'news',
            'action'        => 'index',
            'title'         => 'Список новостей',
            'route'         => array(
                'pattern'       => '/{id}',
                'requirements'  => array('id' => '\d+'),
            ),
            'params'        => array(
                'per_page'         => array(
                    'title'        => 'Новостей на страницу',
                    'default'      => 10,
                    'type'         => 'int',
                    'requirements' => '1'
                )
            )
        );
        $usageType = new UsageType();
        $usageType->setName('some');
        $service = new BaseUsageService('some', array('types' => array(), 'group' => array()), 'bundleName');

        $service->parseUsageType($usageType, $arrayDefention);
        $params = $usageType->getParams();
        $route = $usageType->getRoute();

        $this->assertInternalType('array', $params, 'Параметры должны быть массивом');
        $expectedCountParams = count($arrayDefention['params']);
        $this->assertCount( $expectedCountParams,  $params,
            sprintf('Должен содержать %d параметров', $expectedCountParams));

        /**
         * @var \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam $oneParam
         */
        foreach($params as $paramName => $oneParam) {
            $this->assertInstanceOf('\Armd\Bundle\CmsBundle\UsageType\Param\BaseParam', $oneParam,
                'Параметр должен быть потомком UsageType\Param\BaseParam');
            $this->assertEquals($paramName, $oneParam->getName(),
                'Имя параметра и ключ в массиве параметров должны соотвествовать');
            $this->assertArrayHasKey($paramName, $arrayDefention['params'],
                'Имя параметра должно присутствовать в массиве определения параметров');

            $defParam = $arrayDefention['params'][$paramName];
            $this->assertEquals( $defParam['title'], $oneParam->getTitle(),
                'Значение параметра не соответсвует объявленному' );
            $this->assertEquals( $defParam['default'], $oneParam->getDefault(),
                'Значение параметра не соответсвует объявленному' );
            $this->assertEquals( (bool)$defParam['requirements'], $oneParam->isRequirements(),
                'Значение параметра не соответсвует объявленному' );
        }
    }
    
    public function testUndefinedParamsValue()
    {
        $usageType = new UsageType();
        $param = $usageType->getParam('undefined');
        
        $this->assertInstanceOf('\Armd\Bundle\CmsBundle\UsageType\Param\NullParam', $param);
        $this->assertNull($param->getValue());
    }
    
    public function testTemplateParamsValue()
    {
        $usageType = new UsageType();
        $this->assertNull($usageType->getTemplateValue());
        
        $template = new TemplateParam();
        $template->setName('template');
        $template->setValue('default');
                
        $usageType->addParam($template);
        
        $this->assertEquals('default', $usageType->getTemplateValue());
    }
}
