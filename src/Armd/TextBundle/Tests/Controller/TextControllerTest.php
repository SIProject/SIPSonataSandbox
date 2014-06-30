<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\TextBundle\Tests\Controller;

use Armd\Bundle\CmsBundle\UsageType\UsageType;
use Armd\Bundle\CmsBundle\UsageType\Param\IntParam;
use Armd\Bundle\CmsBundle\Model\Theme\Layout;

use PHPUnit_Framework_TestCase;

class TextControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function testItemAction()
    {
        $this->setContainer();

        $controller = $this->getMockBuilder('Armd\\Bundle\\TextBundle\\Controller\\TextController')
             ->setMethods(array('renderCms', 'getDoctrine'))
             ->disableOriginalConstructor()
             ->getMock();

        $controller->expects($this->any())->method('getDoctrine')->will($this->returnValue($this->getDoctrine()));
        $controller->expects($this->any())->method('renderCms')
            ->with($this->equalTo(array('entity' => 'entities')))
            ->will($this->returnValue(true));

        $controller->init(new Layout(),
                           $this->getUsageType(),
                           $this->getMockBuilder('Armd\\Bundle\\CmsBundle\\Manager\\Page')->disableOriginalConstructor()->getMock(),
                           $this->getMockBuilder('Symfony\\Component\\Templating\\EngineInterface')->disableOriginalConstructor()->getMock()
        );
        $controller->itemAction();
    }

    /**
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageType
     */
    public function getUsageType()
    {
        $param = new IntParam();
        $param->setName('text_id');
        $param->setValue(4);

        $usageType = new UsageType();

        $usageType->setParams(array($param));
        return $usageType;
    }

    public function setContainer()
    {
        $this->container = $this->getMockBuilder('Symfony\\Component\\DependencyInjection\\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();

    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function getDoctrine()
    {
        $entityRepository = $this->getMockBuilder('Doctrine\\ORM\\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $entityRepository->expects($this->any())->method('find')
            ->with($this->equalTo(4))
            ->will($this->returnValue('entities'));

        $entityManager = $this->getMockBuilder('Doctrine\\ORM\\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->any())->method('getRepository')->will($this->returnValue($entityRepository));

        $doctrine = $this->getMockBuilder('Symfony\\Bundle\\DoctrineBundle\\Registry')
            ->disableOriginalConstructor()
            ->getMock();
        $doctrine->expects($this->any())->method('getEntityManager')->will($this->returnValue($entityManager));

        return $doctrine;
    }
}
