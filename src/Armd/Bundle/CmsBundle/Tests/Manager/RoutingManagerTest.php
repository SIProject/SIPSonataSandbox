<?php
/*
 * (c) Sukhinin Ilja <isuhinin@armd.ru>
 */

namespace Armd\Bundle\CmsBundle\Tests\Manager;

use PHPUnit_Framework_TestCase;

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;

use Armd\Bundle\CmsBundle\Manager\RoutingManager;

class RoutingManagerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Armd\Bundle\CmsBundle\Manager\RoutingManager
     */
    protected $routingManager;

    public function setUp()
    {
        $request = new Request();
        $request->attributes->set('_controller', 'controller');
        $request->attributes->set('pageId', 1);
        $request->attributes->set('param', 1);

        $this->routingManager = new RoutingManager($request);
    }

    public function testGetActionParams()
    {
        $result = $this->routingManager->getActionParams();

        $this->assertCount(1, $result, "Не верное число параметров REQUEST");
        $this->assertArrayHasKey('param', $result, "Отсутствует параметр param в результирующем списке параметров");
        $this->assertEquals(1, $result['param'], "Неверное значение параметра param");
    }

    public function testMatch()
    {
        $route = new Route('/{page}/{order}');

        $result = $this->routingManager->match($route, '/2/desc');

        $this->assertCount(3, $result, "Неверное число параметров Route");

        $this->assertArrayHasKey('page', $result, "Отсутствует параметр page");
        $this->assertEquals(2, $result['page'], "Неверное значение параметра page");

        $this->assertArrayHasKey('order', $result, "Отсутствует параметр order");
        $this->assertEquals('desc', $result['order'], "Неверное значение параметра order");
    }
}