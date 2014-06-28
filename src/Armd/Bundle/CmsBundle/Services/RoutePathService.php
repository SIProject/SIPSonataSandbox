<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Services;

use Armd\Bundle\CmsBundle\Manager\PageManager;
use Armd\Bundle\CmsBundle\UsageType\BaseUsageService;
use Armd\Bundle\CmsBundle\UsageType\UsageType;
use Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer;
use Armd\Bundle\CmsBundle\UsageType\Param\NullParam;

use Symfony\Component\DependencyInjection\ContainerInterface;

class RoutePathService
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    protected $pageManager;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $router;

    /**
     * @var bool
     */
    protected $absolute = false;

    /**
     * @var bool
     */
    protected $debug = true;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param bool $debug
     * @return RoutePathService
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        /** @var \Symfony\Component\HttpKernel\Kernel $kernel */
        $kernel = $this->container->get('kernel');

        return $kernel->isDebug() && $this->debug;
    }

    /**
     * @param bool $absolute
     * @return RoutePathService
     */
    public function setAbsolute($absolute)
    {
        $this->absolute = $absolute;

        return $this;
    }

    /**
     * @param array $params
     * @param null $usageTypeName
     * @param null $UsageTypeContainerName
     * @param null $usageServiceName
     * @return string
     */
    public function getUrlPath(array $params = array(), $usageTypeName = null, $UsageTypeContainerName = null, $usageServiceName = null)
    {
        if (!$UsageTypeContainerName) {
            $UsageTypeContainerName = $this->getContainerParamService()->getUsageTypeContainerName();
        }

        if (!$usageServiceName) {
            $usageServiceName = $this->getContainerParamService()->getUsageServiceName();
        }

        if (!$usageServiceName && !$UsageTypeContainerName) {
            return '';
        }

        $usageService       = $this->getPageManager()->getUsageService($usageServiceName);
        $usageTypeContainer = $usageService->getContainerType($UsageTypeContainerName);

        if ($usageTypeName) {
            return $this->generateUrlPathByUsageType($usageService, $usageTypeContainer, $usageTypeContainer->getType($usageTypeName), $params);
        }
        return $this->generateUrlPathByUsageTypeContainer($usageService, $usageTypeContainer, $params);
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\BaseUsageService $usageService
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer $usageTypeContainer
     * @param array $params
     * @return string
     */
    public function generateUrlPathByUsageTypeContainer(BaseUsageService $usageService, UsageTypeContainer $usageTypeContainer, array $params = array())
    {
        foreach ($usageTypeContainer->getTypes() as $usageType) {
            try {
                return $this->generateUrlPath(array($usageService->getName(), $usageTypeContainer->getName(), $usageType->getName()),
                    $params,
                    $this->checkUseContentStream($usageService, $usageTypeContainer, $usageType, $params));
            } catch (\Exception $e) {}
        }

        $this->FindRouteException($usageService->getName(), $usageTypeContainer->getName(), $params);

        return '';
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\BaseUsageService $usageService
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer $usageTypeContainer
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageType $usageType
     * @param array $params
     * @return string
     */
    public function generateUrlPathByUsageType(BaseUsageService $usageService, UsageTypeContainer $usageTypeContainer, UsageType $usageType, array $params = array())
    {
        try {
            return $this->generateUrlPath(array($usageService->getName(), $usageTypeContainer->getName(), $usageType->getName()),
                $params,
                $this->checkUseContentStream($usageService, $usageTypeContainer, $usageType, $params));
        } catch (\Exception $e) {
            $this->FindRouteException($usageService->getName(), $usageTypeContainer->getName(), $params);
        }

        return '';
    }

    /**
     * @param $usageServiceName
     * @param $usageTypeContainerName
     * @param $params
     * @throws \RuntimeException
     */
    public function FindRouteException($usageServiceName, $usageTypeContainerName, $params)
    {
        if ($this->isDebug()) {
            throw new \RuntimeException(
                sprintf("Not match url for Module '%s', UsageType '%s' with params '%s'",
                    $usageServiceName, $usageTypeContainerName, join(',', array_keys($params))
                ));
        }
    }

    /**
     * @param $routeParts
     * @param array $params
     * @param bool $haveStream
     * @return string
     */
    public function generateUrlPath($routeParts, array $params, $haveStream = false)
    {
        $this->setStreamRoutePart($routeParts, $params, $haveStream);
        $this->setLocaleRoutePart($routeParts, $params);

        return $this->getRouter()->generate(join('.', $routeParts), $params, $this->absolute);
    }

    /**
     * @param $routeParts
     * @param $params
     * @param bool $haveStream
     */
    public function setStreamRoutePart(&$routeParts, &$params, $haveStream = false)
    {
        $stream = null;
        if (isset($params['stream'])) {
            $stream = $params['stream'];
            unset($params['stream']);
        }

        $stream = $stream? $stream: $this->getPageManager()->getStream();
        if ($stream && $haveStream) {
            array_push($routeParts,'stream_' . $stream);
        }
    }

    /**
     * @param $routeParts
     * @param $params
     */
    public function setLocaleRoutePart(&$routeParts, &$params)
    {
        $locale = null;
        if (isset($params['locale'])) {
            $locale = $params['locale'];
            unset($params['locale']);
        }

        if (!$locale && $this->container->has('request')) {
            $locale = $this->container->get('request')->getLocale();
        }

        if (!$locale) {
            $locale = $this->container->getParameter('locale');
        }

        array_push($routeParts, $locale);
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\BaseUsageService $usageService
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer $UsageTypeContainer
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageType $usageType
     * @param array $parmas
     * @return bool
     */
    public function checkUseContentStream(BaseUsageService $usageService, UsageTypeContainer $UsageTypeContainer, UsageType $usageType, $parmas = array())
    {
        if (!($usageType->getParam('content_stream') instanceof NullParam)) {return true;}

        if (!($UsageTypeContainer->getParam('content_stream') instanceof NullParam)) {return true;}

        if (!($usageService->getParam('content_stream') instanceof NullParam)) {return true;}

        if (isset($parmas['stream'])) {return true;}

        return false;
    }

    /**
     * @return \Symfony\Component\Routing\Router
     */
    public function getRouter()
    {
        return $this->container->get('router');
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    public function getPageManager()
    {
        return $this->container->get('armd_cms.page_manager');
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Services\ContainerParamsService
     */
    public function getContainerParamService()
    {
        return $this->container->get('armd_cms.container_params_service');
    }
}