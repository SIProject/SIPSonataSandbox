<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Tests\UsageType;

use Armd\Bundle\CmsBundle\UsageType\BaseUsageService;
use Armd\Bundle\CmsBundle\Form\Type\selectUsageServiceType;

use Symfony\Component\Form\Extension\Core\ChoiceList\ArrayChoiceList;

use PHPUnit_Framework_TestCase;

class selectUsageTypeTest extends PHPUnit_Framework_TestCase
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

        $usageParamsType = new selectUsageServiceType($this->container);

        $result = $usageParamsType->getDefaultOptions();

        $this->ArrayHasKey('choice_list', $result, "Не содержит сервисов");

        $this->assertNotSame(new ArrayChoiceList(array()), $result['choice_list'], 'Не верный тип списка сервисов');

        $this->assertCount(1, $result['choice_list']->getChoices(), "Число сервисов не соответствует заданному");

        $choise = $result['choice_list']->getChoices();
        $this->ArrayHasKey('some', $choise, "Не содержит сервиса some");
        $this->assertEquals('some', $choise['some'], 'Не верное название сервиса some');
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