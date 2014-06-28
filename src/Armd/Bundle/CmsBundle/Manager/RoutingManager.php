<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Manager;

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Router;

class RoutingManager
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $usageServieName;

    /**
     * @var string
     */
    protected $usageTypeContainerName;
    /**
     * @var string
     */
    protected $usageTypeName;

    /**
     * @return \Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(Request $request = null)
    {
        if ($request) {
            $this->request = $request;

            $routeNameArray = explode('.', $this->request->get('_route'));
            if ( count($routeNameArray) >= 3 ) {
                $this->usageServieName        = $routeNameArray[0];
                $this->usageTypeContainerName = $routeNameArray[1];
                $this->usageTypeName          = $routeNameArray[2];
            }
        }
    }

    /**
     * @return string
     */
    public function getUsageServieName()
    {
        return $this->usageServieName;
    }

    /**
     * @return string
     */
    public function getUsageTypeContainerName()
    {
        return $this->usageTypeContainerName;
    }

    /**
     * @return string
     */
    public function getUsageTypeName()
    {
        return $this->usageTypeName;
    }

    /**
     * @return array
     */
    public function getActionParams()
    {
        $params = array();
        foreach ($this->request->attributes->keys() as $key) {
            if (strpos($key, '_') !== 0) {
                $params[$key] = $this->request->attributes->get($key);
            }
        }

        return $params;
    }

    /**
     * @param null|\Symfony\Component\Routing\Route $route
     * @param string $pathInfo
     * @return array
     */
    public function match(Route $route = null, $pathInfo = '/')
    {
        if ( !$route ) { $route = new Route('/', array('_controller' => 'ArmdCmsBundle:CatchAll:index'));}
        $routes = new RouteCollection();

        $routes->add('ArmdCmsBundle_catchAll', $route);
        $matcher = new UrlMatcher($routes, new RequestContext());

        return $matcher->match($pathInfo);
    }

    /**
     * @param \Symfony\Component\Routing\Route $route
     * @param array $params
     * @param string $prefics
     * @return string
     */
    public function generate(Route $route, array $params = array(), $prefics = '/')
    {
        $routes = new RouteCollection();
        $routes->add('InputRoute', $route);

        $generator = new UrlGenerator($routes,
                                      new RequestContext($this->getRouter()->getContext()->getBaseUrl() . $prefics));

        return str_replace('//', '/', $generator->generate('InputRoute', $params));
    }

    /**
     * @param \Symfony\Component\Routing\Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @return \Symfony\Component\Routing\Router
     */
    public function getRouter()
    {
        return $this->router;
    }
}