<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\ContentAbstractBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

/**
 * Base content controller
 */
class Controller extends BaseController
{
    /**
     * @var \Armd\Bundle\CmsBundle\Model\Layout
     */
    protected $layout;

    /**
     * @var \Armd\Bundle\CmsBundle\UsageType\UsageType;
     */
    protected $params;

    /**
     * @var \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    protected $pageManager;

    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var bool
     */
    protected $is_main;

    /**
     * @var mixed
     */
    protected $controllerParams;

    /**
     * @var \Armd\Bundle\CmsBundle\UsageType\BaseUsageService
     */
    protected $usageService;

    /**
     * @var \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer
     */
    protected $usageContainer;

    /**
     * @var \Doctrine\Common\Cache\MemcachedCache
     */
    protected $cache;

    /**
     * @return string
     */
    public function getPath()
    {
        $moduleName     = $this->getParams()->getModuleName();
        $controllerName = $this->getParams()->getController();
        $actionName     = $this->getParams()->getAction();

        $themeService = $this->getPageManager()->getTemplateService();
        $path = $themeService->getTemplatePath($moduleName, $controllerName, $actionName, $this->getParams()->getTemplateValue());
        return $path . '.html.twig';
    }

    /**
     * @param $name
     * @param null $default
     * @return mixed|null
     */
    public function getParamsValue($name, $default = null)
    {
        $value = $this->getParams()->getParam($name)->getValue();

        return $value ? $value : $default;
    }

    /**
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderCms(array $parameters = array())
    {
        if ($this->getIsMain()) {
            $this->setContainerParams($parameters);
        }

        $response = $this->render($this->getPath(), $parameters);
        $response->setSharedMaxAge($this->getParams()->getParam('SharedMaxAge')->getValue());
        $this->clearContainerSettings();

        return $response;
    }

    /**
     * @return \Armd\ContentAbstractBundle\Repository\BaseContentRepository
     */
    protected function getEntityRepository($alias = 't', $entityName = null)
    {
        $entityName = $entityName? $entityName: $this->getEntityName();
        $repository = $this->getDoctrine()->getRepository($entityName);
        $repository->createQueryBuilder($alias);

        return $repository;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        $entityName = $this->getParams()->getController();

        return "SIPResourceBundle:{$entityName}";
    }

    /**
     * @param $responseParams
     */
    public function setContainerParams($responseParams)
    {
        $routeParams = array();
        foreach ($this->getRequest()->attributes as $attributeName => $atribute) {
            if (!(strpos($attributeName, '_') === 0)) {
                $routeParams[$attributeName] = $atribute;
            }
        }

        $this->getContainerParamService()->setContainerRequestParams($routeParams);
        $this->getContainerParamService()->setUsageServiceName($this->getRequestParam('_service_name'));
        $this->getContainerParamService()->setUsageTypeContainerName($this->getRequestParam('_type_container'));
        $this->getContainerParamService()->setContainerResponseParams($responseParams);
    }

    /**
     * @return array
     */
    public function getRequestParams()
    {
        return $this->getContainerParamService()->getContainerRequestParams();
    }

    /**
     * @return array
     */
    public function getResponseParams()
    {
        return $this->getContainerParamService()->getContainerResponseParams();
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Services\ContainerParamsService
     */
    public function getContainerParamService()
    {
        return $this->get('armd_cms.container_params_service');
    }

    /**
     * @param \Doctrine\ORM\Query $query
     * @param int $page
     * @return \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination
     */
    public function getPagination(\Doctrine\ORM\Query $query, $page)
    {
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($query, $page, $this->getLimit());

        if ((int)$this->getParams()->getParam('standalone')->getValue()) {
            $pagination->setUsedRoute($this->getPageManager()
                                           ->buildRouteName(array($this->getRequestParam('_service_name'),
                                                                  $this->getRequestParam('_type_container'),
                                                                  $this->getRequestParam('_type')),
                                                            $this->getPageManager()
                                                                 ->checkUseContentStream($this->getUsageService(),
                                                                                         $this->getUsageContainer(),
                                                                                         $this->getParams())));
            $pagination->setParam('path', null);
            $pagination->setParam('controller', null);
        }
        return $pagination;
    }

    public function getLimit()
    {
        return $this->getParamsValue('per_page');
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    public function getPageManager()
    {
        if (!$this->pageManager) {
            $this->pageManager = $this->get('armd_cms.page_manager');
        }

        return $this->pageManager;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Model\Layout
     */
    public function getLayout()
    {
        if (!$this->layout) {
            $this->layout = $this->getRequestParam('_layout');
        }

        return $this->layout;
    }

    /**
     * @return \Symfony\Component\Templating\EngineInterface
     */
    public function getTemplating()
    {
        if (!$this->templating) {
            $this->templating = $this->get('templating');
        }

        return $this->templating;
    }

    /**
     * @return bool
     */
    public function getIsMain()
    {
        if (!$this->is_main) {
            $this->is_main = $this->getRequest()->attributes->get('_is_main');
        }

        return $this->is_main;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageType
     */
    public function getParams()
    {
        if (!$this->params) {
            $this->params = $this->getUsageContainer()->getType($this->getRequestParam('_type'));
            $settings     = $this->getRequestParam('_params')? $this->getRequestParam('_params'): array();

            $this->getUsageService()->paramsBuild($this->params, $this->getUsageContainer(), $settings);

            if (isset($settings['content_stream'])) {
                $this->getPageManager()->setStream($settings['content_stream']);
            }
        }

        return $this->params;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function getRequestParam($name)
    {
        if ($this->getRequest()->attributes->has($name)) {
            return $this->getRequest()->attributes->get($name);
        }

        return null;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\UsageType\BaseUsageService
     */
    public function getUsageService()
    {
        if (!$this->usageService) {
            $this->usageService = $this->getPageManager()->getUsageService($this->getRequestParam('_service_name'));
        }

        return $this->usageService;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer
     */
    public function getUsageContainer()
    {
        if (!$this->usageContainer) {
            $this->usageContainer = $this->getUsageService()->getContainerType($this->getRequestParam('_type_container'));
        }

        return $this->usageContainer;
    }

    /**
     * Clear container settings for each container
     */
    public function clearContainerSettings()
    {
        $this->usageService = null;
        $this->usageContainer = null;
        $this->params = null;
        $this->is_main = null;
        $this->layout = null;
    }

    /**
     * @return \Doctrine\Common\Cache\MemcachedCache
     */
    public function getCache()
    {
        if (!$this->cache) {
            $this->cache = $this->get('system_cache');
        }

        return $this->cache;
    }

    /**
     * @param $cacheIdentifier
     * @return string
     */
    public function getEntityCacheKey($cacheIdentifier)
    {
        if (is_array($cacheIdentifier)) {
            $cacheIdentifier = empty($cacheIdentifier) ? 0 : join('_', $cacheIdentifier);
        }

        return $this->getDoctrine()->getRepository($this->getEntityName())->getClassName() .
               '_item_' . $cacheIdentifier . '_' .
               $this->container->get('request')->getLocale();
    }
}