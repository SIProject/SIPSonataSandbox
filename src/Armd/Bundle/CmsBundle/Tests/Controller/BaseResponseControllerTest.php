<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */

namespace Armd\Bundle\CmsBundle\Tests\Controller;

use PHPUnit_Framework_TestCase;

use Symfony\Component\HttpFoundation\Response;

class BaseResponseControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Armd\Bundle\CmsBundle\Controller\BaseResponseController
     */
    protected $controller;

    public function setUp()
    {
        $this->controller = $this->getMockForAbstractClass('Armd\\Bundle\\CmsBundle\\Controller\\BaseResponseController');
    }

    public function testSetResponse()
    {
        $result = $this->controller->setResponse();

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\Response', $result, 'Не верный тип возвращаемого значения');

        $this->assertEquals(200, $result->getStatusCode(), 'Не соответствует код результата');

        $this->assertEquals(array('data' => null,
                                  'status' => 200,
                                  'error' => ''),
                            json_decode($result->getContent(), true),
                            'Не соответствует контента результата');
    }

    public function testSetExeptionResponseDefaultAtributs()
    {
        $result = $this->controller->setExeptionResponse();

        $this->assertInstanceOf('Symfony\\Component\\HttpFoundation\\Response', $result, 'Не верный тип возвращаемого значения');

        $this->assertEquals(403, $result->getStatusCode(), 'Не соответствует код результата');

        $this->assertEquals(array('status' => 403,
                                  'error' => 'access denied'),
                            json_decode($result->getContent(), true),
                            'Не соответствует контента результата');
    }

    public function testSetExeptionResponse()
    {
        $result = $this->controller->setExeptionResponse('Error Message', 500);

        $this->assertEquals(500, $result->getStatusCode(), 'Не соответствует код результата');

        $this->assertEquals(array('status' => 500,
                                  'error' => 'Error Message'),
                            json_decode($result->getContent(), true),
                            'Не соответствует контента результата');
    }
}