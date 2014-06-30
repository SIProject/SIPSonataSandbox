<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\MainMenuBundle\Twig\Extension;

use Twig_Extension;
use Twig_Function_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuTwigExtension extends Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @var \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    protected $pageManager;

    /**
     * @var \Symfony\Component\Routing\Router
     */
    protected $router;

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
            'root_path'       => new Twig_Function_Method($this, 'getRootPath'),
            'is_current_path' => new Twig_Function_Method($this, 'isCurrentPath'),
        );
    }

    /**
     * @return string
     */
    public function getRootPath()
    {
        if ($rootPage = $this->getPageRepository()->find($this->getPageManager()->getCurrentPage()->getRoot())) {
            return $this->getRequest()->getBaseUrl() . $rootPage->getUrl();
        }

        return $this->getRequest()->getPathInfo();
    }

    /**
     * @param $path
     * @return bool
     */
    public function isCurrentPath($path)
    {
        try {
            $route = $this->getRouter()->match(str_replace($this->getRequest()->getBaseUrl(), '', $path));
            return ($route['_route'] == $this->getRequest()->get('_route'));
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = $this->container->get('request');
        }

        return $this->request;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    public function getPageManager()
    {
        if (!$this->pageManager) {
            $this->pageManager = $this->container->get('armd_cms.page_manager');
        }

        return $this->pageManager;
    }

    /**
     * @return \Symfony\Component\Routing\Router
     */
    public function getRouter()
    {
        if (!$this->router) {
            $this->router = $this->container->get('router');
        }

        return $this->router;
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
     * @return \Armd\Bundle\CmsBundle\Entity\PageRepository
     */
    public function getPageRepository()
    {
        return $this->getDoctrine()->getRepository('ArmdCmsBundle:Page');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'armd_cms_main_menu_twig_extension';
    }
}