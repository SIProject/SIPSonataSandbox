<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Manager;

use Doctrine\Common\Collections\Collection;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Armd\Bundle\CmsBundle\Entity\Page;
use Armd\Bundle\CmsBundle\Entity\Container;
use Armd\Bundle\CmsBundle\UsageType\BaseUsageService;
use Armd\Bundle\CmsBundle\UsageType\UsageType;
use Armd\Bundle\CmsBundle\UsageType\Param\NullParam;
use Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer;
use Armd\Bundle\CmsBundle\Entity\ContainerIntrface;

class PageManager
{
    /**
     * @var \Armd\Bundle\CmsBundle\Entity\Page
     */
    protected $currentPage;

    /**
     * @var /Symfony\Component\Templating\EngineInterface
     */
    protected $templating;

    /**
     * @var \Armd\Bundle\CmsBundle\UsageType\BaseUsageService[]
     */
    protected $usageServices = array();

    /**
     * @var \Armd\Bundle\CmsBundle\Entity\PageManager
     */
    protected $pageManager;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $stream;

    /**
     * @var /Symfony\Component\HttpFoundation\Response
     */
    protected $masterRequestResponse;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, array $usageServices = array())
    {
        $this->container     = $container;
        $this->templating    = $this->container->get('templating');
        $this->pageManager   = $this->container->get('armd_cms.entity_manager.page');
        $this->usageServices = $usageServices;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Response $masterRequestResponse
     */
    public function setMasterRequestResponse($masterRequestResponse)
    {
        $this->masterRequestResponse = $masterRequestResponse;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMasterRequestResponse()
    {
        return $this->masterRequestResponse;
    }

    /**
     * @param $stream
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    /**
     * @return string
     */
    public function getStream()
    {
        return $this->stream;
    }

    /**
     * @param $id
     * @return \Armd\Bundle\CmsBundle\Entity\Page
     */
    public function getPageById($id)
    {
        if (!$this->currentPage) {
            $this->currentPage = $this->pageManager->findById($id);
        }

        return $this->currentPage;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Entity\Page
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\Page $page
     */
    public function setCurrentPage($page)
    {
        $this->currentPage = $page;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Entity\PageManager
     */
    public function getPageManager()
    {
        return $this->pageManager;
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\Page $page
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \LogicException
     */
    public function renderPage(Page $page, Request $request)
    {
        if ($page->getToFirstChild()) {
            if ($firstChild = $this->pageManager->findOneBy(array('parent' => $page))) {
                return new \Symfony\Component\HttpFoundation\RedirectResponse($request->getBaseUrl() . $firstChild->getUrl());
            }
            throw new \LogicException(sprintf("Page : '%s' hasn't children", $page->getId()));
        }

        if (!($pageType = $page->getPageType())) {
            throw new \LogicException(sprintf("Page type for page id: '%s' not found", $page->getId()));
        }

        $this->setPageContainersCollection($pageType->getContainers(), $page);

        if ($this->getMasterRequestResponse() instanceof Response) {
            return $this->getMasterRequestResponse();
        }

        $layoutPath = $this->getTemplateService()->getLayout($pageType->getLayout())->getLayoutPath();
        return $this->templating->renderResponse($layoutPath, array('manager' => $this));
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\Container[] $containers
     * @return array
     */
    public function renderPageContainers(Collection $containers)
    {
        $containersContent = array();
        $mainContainerContent = array();

        foreach ($containers as $container) {
            if ($container->getIsMain()) {
                $renderedContainer = $this->renderContainer($container);
                $mainContainerContent[$container->getArea()->getName()] = $renderedContainer;

                if ($renderedContainer instanceof Response) {
                    $this->setMasterRequestResponse($renderedContainer);
                    return array();
                }
            }
        }

        foreach ($containers as $container) {
            if (!$container->getIsMain()) {
                $renderedContainer = $this->renderContainer($container);
                $containersContent[$container->getArea()->getName()] = $renderedContainer;

                if ($renderedContainer instanceof Response) {
                    $this->setMasterRequestResponse($renderedContainer);
                    return array();
                }
            }
        }

        return array_merge($mainContainerContent, $containersContent);
    }

     /**
      * @param \Armd\Bundle\CmsBundle\Entity\ContainerIntrface $container
      * @return string|\Symfony\Component\HttpFoundation\Response
      */
    public function renderContainer(ContainerIntrface $container)
    {
        $response = $this->responseContainer($container);

        if ($response->isSuccessful()) {
            return $response->getContent();
        }

        return $response;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $containers
     */
    public function setPageContainersCollection(Collection $containers)
    {
        $this->getContainerCollectionService()->setContainersContant($this->renderPageContainers($containers));
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\Container $container
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \RuntimeException
     */
    public function responseContainer(Container $container)
    {
        $response = $this->getContainerService()->execute($this, $container);

        if (!$response instanceof Response) {
            throw new \RuntimeException('A container service must return a Response object');
        }

        return $response;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\UsageType\BaseUsageService[]|array
     */
    public function getUsageServices()
    {
        $usageServices = array();
        foreach ($this->usageServices as $usageServiceId) {
            if ($this->container->has($usageServiceId)) {
                array_push($usageServices, $this->container->get($usageServiceId));
            }
        }

        return $usageServices;
    }

    /**
     * @param $usageServiceName
     * @return bool
     */
    public function hasUsageService($usageServiceName)
    {
        $usageServiceId = $usageServiceName . '.usagetype';
        return in_array($usageServiceId, $this->usageServices) && $this->container->has($usageServiceId);
    }

    /**
     * @param $usageServiceName
     * @return \Armd\Bundle\CmsBundle\UsageType\BaseUsageService
     */
    public function getUsageService($usageServiceName)
    {
        $usageServiceId = $usageServiceName . '.usagetype';
        if (in_array($usageServiceId, $this->usageServices) && $this->container->has($usageServiceId)) {
            return $this->container->get($usageServiceId);
        } else {
            return false;
        }
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Services\ContainerService
     */
    public function getContainerService()
    {
        return $this->container->get('armd_cms.container_service');
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Services\ContainerCollectionService
     */
    public function getContainerCollectionService()
    {
        return $this->container->get('armd_cms.container_collection_service');
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Services\TemplateService
     */
    public function getTemplateService()
    {
        return $this->container->get('armd_cms.template_service');
    }
}