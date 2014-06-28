<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */

namespace Armd\Bundle\CmsBundle\Tests\UsageType;

use Armd\Bundle\CmsBundle\UsageType\BaseUsageService;
use Armd\Bundle\CmsBundle\Form\Type\usageParamsType;

use PHPUnit_Framework_TestCase;

class UsageParamsTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function testGetDefaultOptions()
    {
        $pagemanager = $this->container = $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Manager\\Page')
            ->disableOriginalConstructor()
            ->getMock();
        $this->container->expects($this->any())->method('getUsageServices')->will($this->returnValue(array($this->getUsageService())));

        $this->container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->container->expects($this->any())->method('get')->will($this->returnValue($pagemanager));

        $usageParamsType = new usageParamsType($this->container);

        $result = $usageParamsType->getDefaultOptions();

        $this->ArrayHasKey('keys', $result, "Не содержит параметров");

        $this->assertCount(3, $result['keys'], "Число представлений не соответствует заданному");

        $this->ArrayHasKey('item', $result['keys'], "Не содержит представления item");
        $this->ArrayHasKey('list', $result['keys'], "Не содержит представления list");

        $this->assertEquals('sonata_type_immutable_array', $result['keys']['some:item'][1], 'Не верный тип параметра формы');

        $this->ArrayHasKey('attr', $result['keys']['some:item'][2], "Не содержит опции attr");
        $this->assertEquals('hide', $result['keys']['some:item'][2]['attr']['class'], 'Поле должно быть скрытым');

        $this->ArrayHasKey('keys', $result['keys']['some:item'][2], "Не содержит опции keys");
        $this->assertCount(1, $result['keys']['some:item'][2]['keys'], "Число параметров не соответствует заданному");
        $this->assertEquals('hidden', $result['keys']['some:item'][2]['keys'][0][1], 'Поле должно быть скрытым');

        $this->assertEquals('sonata_type_immutable_array', $result['keys']['some:list'][1], 'Не верный тип параметра формы');

        $this->ArrayHasKey('attr', $result['keys']['some:list'][2], "Не содержит опции attr");
        $this->assertEquals('hide', $result['keys']['some:list'][2]['attr']['class'], 'Поле должно быть скрытым');

        $this->ArrayHasKey('keys', $result['keys']['some:list'][2], "Не содержит опции keys");
        $this->assertCount(1, $result['keys']['some:list'][2]['keys'], "Число параметров не соответствует заданному");
        $this->assertEquals('hidden', $result['keys']['some:list'][2]['keys'][0][1], 'Поле должно быть скрытым');
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