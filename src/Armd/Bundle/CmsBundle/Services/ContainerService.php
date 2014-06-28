<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Armd\Bundle\CmsBundle\Entity\PageContainer;
use Armd\Bundle\CmsBundle\Entity\Container;
use Armd\Bundle\CmsBundle\Entity\Page;
use Armd\Bundle\CmsBundle\Manager\PageManager;
use Armd\Bundle\CmsBundle\Manager\RoutingManager;
use Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer;

class ContainerService
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Symfony\Component\HttpKernel\DependencyInjection\ContainerAwareHttpKernel
     */
    protected $kernel;

    /**
     * @var \Armd\Bundle\CmsBundle\Manager\RoutingManager
     */
    protected $routingManager;

    /**
     * @var bool
     */
    protected $mainContainer = false;

    /**
     * @var array
     */
    protected $containersPool = array();

    /**
     * @param $name
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     * @param \Armd\Bundle\CmsBundle\Services\ContainerInterface $container
     */
    public function __construct($name, HttpKernelInterface $kernel, ContainerInterface $container)
    {
        $this->container = $container;
        $this->kernel    = $kernel;
        $this->request = $this->container->get('request');
        $this->routingManager = new RoutingManager($this->request);
    }

    /**
     * Execute container Controller:Action by UsageType
     *
     * @param \Armd\Bundle\CmsBundle\Manager\PageManager $manager
     * @param \Armd\Bundle\CmsBundle\Entity\Container $container
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function execute(PageManager $manager, Container $container)
    {
        $page = $manager->getCurrentPage();
        $this->mainContainer = $container->getIsMain();

        $pageContainer = $this->getPageContainer($page, $container);
        $usageServiceName = $this->getUsageServices($container, $pageContainer);
        $usageTypeContainer = $this->getUsageType($container, $pageContainer);

        $actionContent = new Response();
        if ($usageService = $manager->getUsageService($usageServiceName)) {

            $usageContainer = $usageService->getContainerType($usageTypeContainer);

            $usageType = $this->matchUsageType($usageContainer);

            $settings = $usageService->getRealParams($usageType,
                                                     $usageContainer,
                                                     $pageContainer? $pageContainer: $container);

            $controllerService = $usageService->getName() . '.controller.' . $usageType->getController();

            $requestParams = $this->mainContainer? $this->routingManager->getActionParams():
                                                   ($usageType->getRoute()? $usageType->getRoute()->getDefaults():array());

            $attributes = array_merge($requestParams,
                array('_service_name'   => $usageServiceName,
                      '_type_container' => $usageTypeContainer,
                      '_type'           => $usageType->getName(),
                      '_params'         => $settings,
                      '_is_main'        => $this->mainContainer));

            $uniqueContainerKey = http_build_query($attributes);
            if (isset($this->containersPool[$uniqueContainerKey])) {
                return $this->containersPool[$uniqueContainerKey];
            }

            $actionContent = $this->render($controllerService . ':' . $usageType->getAction() . 'Action',
                array('attributes' => $attributes, 'query' => $this->request->query->all()));

            $this->containersPool[$uniqueContainerKey] = $actionContent;
        }

        return $actionContent;
    }

    /**
     * Found usage type by request params
     *
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer $container
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageType
     * @throws \LogicException
     */
    protected function matchUsageType(UsageTypeContainer $container) {
        if ( $this->mainContainer && $this->routingManager->getUsageTypeName() ) {
            return $container->getType($this->routingManager->getUsageTypeName());
        }
        foreach($container->getTypes() as $usageType) {
            try {
                $this->routingManager->match($usageType->getRoute());
                return  $usageType;
            } catch (\Exception $e) {}
        }
        throw new \LogicException('Container "' . $container->getName() . '" dont have acceptable usage types');
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\Page $page
     * @param \Armd\Bundle\CmsBundle\Entity\Container $container
     * @return \Armd\Bundle\CmsBundle\Entity\PageContainer
     */
    public function getPageContainer(Page $page, Container $container)
    {
        foreach ( $page->getPageContainers() as $pageContainer ) {
            if ( $pageContainer->getContainer()->getId() == $container->getId() ) {
                return $pageContainer;
            }
        }
        return null;
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\PageContainer $pageContainer
     * @param \Armd\Bundle\CmsBundle\Entity\Container $container
     * @return string
     */
    public function getUsageServices(Container $container, PageContainer $pageContainer = null)
    {
        return $pageContainer?
                    $pageContainer->getUsageService():
                    $container->getUsageService();
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\PageContainer $pageContainer
     * @param \Armd\Bundle\CmsBundle\Entity\Container $container
     * @return string
     */
    public function getUsageType(Container $container, PageContainer $pageContainer = null)
    {
        return $pageContainer?
                    $pageContainer->getUsageType():
                    $container->getUsageType();
    }

    /**
     * @param $controller
     * @param array $options
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($controller, array $options = array())
    {
        $options['attributes']['_route'] = $this->request->get('_route');
        $options['attributes']['_page_id'] = $this->request->get('_page_id');
        $options['attributes']['_site_id'] = $this->request->get('_site_id');
        $options['attributes']['_controller'] = $controller;

        $subRequest = $this->request->duplicate($options['query'], null, $options['attributes']);
        return $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
}