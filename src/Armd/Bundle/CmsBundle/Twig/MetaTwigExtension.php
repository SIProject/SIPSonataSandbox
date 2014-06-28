<?php
    /*
    * (c) Stepanov Andrey <isteep@gmail.com>
    */
namespace Armd\Bundle\CmsBundle\Twig;

use Twig_Extension;
use Twig_Function_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MetaTwigExtension extends Twig_Extension
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
     * @var \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    protected $pageManager;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getFunctions() {
        return array(
            'page_parameter'     => new Twig_Function_Method($this, 'getPageParameter'),
            'page_logo' => new Twig_Function_Method($this, 'getPageLogo'),
            'site_parameter'    => new Twig_Function_Method($this, 'getSiteParameter'),
            'site_logo'    => new Twig_Function_Method($this, 'getSiteLogo'),
        );
    }

    /**
     * @param $key
     * @return null|string
     */
    public function getPageParameter($key)
    {
        return $this->getParameterByKey($this->getPage()->getParameters(), $key);
    }

    /**
     * @return string
     */
    public function getPageLogo()
    {
        return $this->getPage()->getLogo();
    }

    /**
     * @param $key
     * @return null|string
     */
    public function getSiteParameter($key)
    {
        return $this->getParameterByKey($this->getSite()->getParameters(), $key);
    }

    /**
     * @return string
     */
    public function getSiteLogo()
    {
        return $this->getSite()->getLogo();
    }

    /**
     * @param array $parameters
     * @param $key
     * @return null
     */
    public function getParameterByKey($parameters, $key)
    {
        foreach ($parameters as $parameter) {
            if ($parameter['key'] == $key) {
                return $parameter['value'];
            }
        }

        return null;
    }

    /**
     * @param $siteId
     * @return \Armd\Bundle\CmsBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->getPageManager()->getCurrentPage()->getSite();
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Entity\Page
     */
    public function getPage()
    {
        return $this->getPageManager()->getCurrentPage();
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

    public function getName()
    {
        return 'armd_cms_meta_twig_extension';
    }
}