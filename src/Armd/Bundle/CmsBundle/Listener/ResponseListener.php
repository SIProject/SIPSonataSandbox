<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Armd\Bundle\CmsBundle\Manager\PageManager;

/**
 * This class redirect the onCoreResponse event to the correct
 * cms manager upon user permission
 */
class ResponseListener
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;

    /**
     * @var \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    protected $cmsManager;

    /**
     * @var array
     */
    protected $ignoreRoutePatterns;

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Routing\Router $router
     * @param \Armd\Bundle\CmsBundle\Manager\PageManager $cmsManager
     * @param array $ignoreRoutePatterns
     */
    public function __construct(Router $router, PageManager $cmsManager, array $ignoreRoutePatterns)
    {
        $this->router              = $router;
        $this->cmsManager          = $cmsManager;
        $this->ignoreRoutePatterns = $ignoreRoutePatterns;
    }

    /**
     * filter the `core.response` event to decorated the action
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
     * @return void
     */
    public function onCoreResponse(FilterResponseEvent $event)
    {
        $request  = $event->getRequest();

        if (!$this->cmsManager || !$this->isDecorate($request, $event->getRequestType(), $event->getResponse())) {
            return;
        }

        $response = $this->cmsManager->renderPage($this->getCurrentPage($request->attributes->get('_page_id')), $request);
        $event->setResponse($response);
    }

    /**
     * @param $id
     * @return \Armd\Bundle\CmsBundle\Entity\Page
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function getCurrentPage($id)
    {
        $page = $this->cmsManager->getPageByid($id);
        if (!$page) {
            throw new NotFoundHttpException('The current url does not exist!');
        }
        return $page;
    }

    /**
     * return true is the page can be decorate with an outter template
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $requestType
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @return bool
     */
    public function isDecorate(Request $request, $requestType, Response $response)
    {
        if ($response->getStatusCode() != 200) {
            return false;
        }

        if ($requestType != HttpKernelInterface::MASTER_REQUEST) {
            return false;
        }

        if (($response->headers->get('Content-Type') ?: 'text/html') != 'text/html') {
            return false;
        }

        if ($request->headers->get('x-requested-with') == 'XMLHttpRequest') {
            return false;
        }

        if (!($routeName = $request->get('_route'))) {
            try {
                $route = $this->router->match( $request->getPathInfo() );
                $routeName = $route["_route"];
            } catch (\Exception $e) {}
        }

        if ( $routeName == null ) {
            $routeName = 'ArmdCmsBundle_catchAll';
        }

        return $this->isRouteNameDecorate($routeName);
    }

    /**
     * @param string $routeName
     * @return bool
     */
    public function isRouteNameDecorate($routeName)
    {
        foreach ($this->ignoreRoutePatterns as $routePattern) {
            if (preg_match($routePattern, $routeName)) {
                return false;
            }
        }

        return true;
    }
}