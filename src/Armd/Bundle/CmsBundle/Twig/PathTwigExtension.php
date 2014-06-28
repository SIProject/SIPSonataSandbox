<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Twig;

use Twig_Extension;
use Twig_Function_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PathTwigExtension extends Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\Common\Cache\MemcachedCache
     */
    protected $cache;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getFunctions() {
        return array(
            'cmsPath'     => new Twig_Function_Method($this, 'getPath'),
            'cmsPathById' => new Twig_Function_Method($this, 'getPathById'),
            'basePath'    => new Twig_Function_Method($this, 'basePath'),
        );
    }

    /**
     * Get page url
     *
     * @param array $params
     * @param string $usageTypeName
     * @param null $UsageTypeContainerName
     * @param string $usageServiceName
     * @param bool $absolute
     * @param null $debug
     * @return string
     */
    public function getPath(array $params = array(), $usageTypeName = null, $UsageTypeContainerName = null, $usageServiceName = null, $absolute = null, $debug = null)
    {
        /** @var \Armd\Bundle\CmsBundle\Services\RoutePathService $routePathService */
        $routePathService = $this->container->get('armd_cms.route_path_service');
        $routePathService->setAbsolute($absolute)
                         ->setDebug($debug);
        return $routePathService->getUrlPath($params, $usageTypeName, $UsageTypeContainerName, $usageServiceName);
    }

    /**
     * @param $pageId
     * @return string
     */
    public function getPathById($pageId)
    {
        if ($this->getCache()->contains('_page_' . $pageId)) {
            return $this->getRequest()->getBaseUrl() . $this->getCache()->fetch('_page_' . $pageId);
        }

        /** @var \Armd\Bundle\CmsBundle\Entity\Page $page */
        if ($page = $this->getDoctrine()->getRepository('ArmdCmsBundle:Page')->find($pageId)) {
            $this->getCache()->save('_page_' . $pageId, $page->getUrl());
            return $this->getRequest()->getBaseUrl() . $page->getUrl();
        }

        return '';
    }

    /**
     * @return string
     */
    public function basePath()
    {
        /**
         * @var \Symfony\Component\Routing\Router $router
         */
        $router = $this->container->get('router');
        $route = $router->match('/');
        return $router->generate($route['_route']);
    }

    public function getName()
    {
        return 'armd_cms_path_twig_extension';
    }

    /**
     * @return \Doctrine\Common\Cache\MemcachedCache
     */
    public function getCache()
    {
        if (!$this->cache) {
            $this->cache = $this->container->get('system_cache');
        }

        return $this->cache;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        if (!$this->doctrine) {
            $this->doctrine = $this->container->get('doctrine');
        }

        return $this->doctrine;
    }

    /**
     * Shortcut to return the request service.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }
}
