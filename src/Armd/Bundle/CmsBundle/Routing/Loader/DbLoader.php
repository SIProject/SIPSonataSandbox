<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Routing\Loader;

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Locale\Locale;

use Armd\Bundle\CmsBundle\Entity\Page;
use Armd\Bundle\CmsBundle\UsageType\UsageType;
use Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer;
use Armd\Bundle\CmsBundle\UsageType\BaseUsageService;
use Armd\Bundle\CmsBundle\Entity\ContainerIntrface;

use Doctrine\ORM\EntityManager;

/**
 * DbLoader loads routes from a Database.
 */
class DbLoader extends Loader
{
    /**
     * 
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var Symfony\Component\DependencyInjection\ContainerInterface;
     */
    protected $container;

    /**
     * @var string
     */
    protected $defaultLang;

    /**
     * @var Symfony\Component\Routing\RouteCollection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $services;

    /**
     * Constructor.
     *
     * @param \Doctrine\ORM\EntityManager $em the Doctrine Entity Manager
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
        $this->defaultLang =$this->container->getParameter('default_lang');
        $this->collection = new RouteCollection();
    }
    
    /**
     * Loads from the database
     *
     * @param string $table    The table that contains the pages (with title, slug and controller, configure this as resource in your routing.yml, provide as type: db )
     * @param string $type     The resource type
     * @return RouteCollection the collection of routes stored in the database table
     */
    public function load($table, $type = null)
    {
        foreach ( $this->getPages($table) as $page ) {
            $routeName = $page->getSlug();

            $containers = (count($page->getPageContainers())? $page->getPageContainers():
                          (count($page->getPageType()->getContainers())? $page->getPageType()->getContainers(): false));

            if ( $containers && $this->hasUsageService($containers[0])) {
                $container = $containers[0];

                $usageService = $this->getUsageService($container->getUsageService());

                try {
                    $usageTypeContainer = $usageService->getContainerType($container->getUsageType());
                } catch (\Exception $e) {
                    continue;
                }

                $usageTypes = $usageTypeContainer->getTypes();

                foreach ($usageTypes as $usageType) {
                    $routeName = $container->getUsageService() . '.' . $container->getUsageType() . '.' . $usageType->getName();
                    if ( $stream = $this->getStream($container, $this->hasStream($usageType, $usageService, $usageTypeContainer)) ) {
                        $routeName .= '.stream_' . $stream;
                    }
                    $this->AddCmsLangRoute($routeName, $page, $usageType->getRoute());
                }
            } else {
                $this->AddCmsLangRoute($routeName, $page);
            }
        }

        return $this->collection;
    }

    /**
     * @param @param \Armd\Bundle\CmsBundle\Entity\ContainerIntrface $container
     * @param $hasStream
     * @return bool
     */
    public function getStream(ContainerIntrface $container, $hasStream)
    {
        if ($hasStream && $container->hasSetting('content_stream')) {
            return $container->getSetting('content_stream');
        }

        return false;
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageType $usageType
     * @param \Armd\Bundle\CmsBundle\UsageType\BaseUsageService $usageService
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer $usageTypeContainer
     * @return bool
     */
    public function hasStream(UsageType $usageType, BaseUsageService $usageService , UsageTypeContainer $usageTypeContainer)
    {
        return $usageType->hasParam('content_stream') ||
               $usageService->hasParam('content_stream') ||
               $usageTypeContainer->hasParam('content_stream');
    }

    /**
     * @param string $table
     * @return Armd\Bundle\CmsBundle\Entity\Page[]
     */
    public function getPages($table)
    {
        $pages = $this->em->getRepository($table)->findAllByMainContainer();
        $this->em->clear();
        return $pages;
    }

    /**
     * @param $container
     * @return bool
     */
    public function hasUsageService($container)
    {
        return (null !== $this->getUsageService($container->getUsageService()));
    }

    /**
     * @param $usageServiceName
     * @return \Armd\Bundle\CmsBundle\UsageType\BaseUsageService
     */
    public function getUsageService($usageServiceName)
    {
        if (isset($this->services[$usageServiceName])) {
            return $this->services[$usageServiceName];
        }
        
        try {
            $service = $this->container->get($usageServiceName . '.usagetype');
        } catch(\Exception $e) {
            $service = null;            
        }        
        
        $this->services[$usageServiceName] = $service;
        
        return $service;
    }

    /**
     * @param string $routeName
     * @param \Armd\Bundle\CmsBundle\Entity\Page $page
     */
    public function AddCmsLangRoute($routeName, Page $page, Route $usageTypeRoute = null)
    {
        if ( strpos($routeName, '-') || strpos($routeName, '-') === 0 ) { $routeName = str_replace('-', '_', $routeName); }

        if (!preg_match('/^[a-z0-9A-Z_.]+$/', $routeName)) { return; }

        $pattern = $page->getUrl(); $requirements = array(); $defaults = array();
        if ( $usageTypeRoute ) {
            $defaults = $usageTypeRoute->getDefaults();
            $pattern = str_replace('//', '', $page->getUrl() . $usageTypeRoute->getPattern());
            $requirements = $usageTypeRoute->getRequirements();
        }

        if ($this->getLocale($pattern) == $this->defaultLang) {
            $this->AddRoute($routeName, $this->getBaseUrl($pattern), $this->getController(), $page, $requirements, $defaults, true);
        }

        $this->AddRoute($routeName, $pattern, $this->getController(), $page, $requirements, $defaults);
    }

    /**
     * @param string $routeName
     * @param $page
     */
    public function AddRoute($routeName, $pattern, $controller, Page $page, array $requirements = array(), array $defaults = array(), $defaultLang = false)
    {
        if ( $this->collection->get($routeName . '.' . ($defaultLang? 'DefaultLang': $this->getLocale($pattern))) ) { $routeName .= '.' . $page->getId(); }

        $routeName .= '.' . ($defaultLang? 'DefaultLang': $this->getLocale($pattern));
        
        $domainDefault = false;
        $hostname = '';
        if($page->getSite()->getDomains()->count()) {
            $hostname = '{_domain}';
            $hosts = array();
            foreach($page->getSite()->getDomains()->getValues() as $domain) {
                $hosts[] = $domain->getPattern();
                if(!$domainDefault) {
                    $domainDefault = $domain->getPattern();
                }
            }
            $requirements['_domain'] = join('|', $hosts);
        }
        
        $this->collection->add($routeName, new Route($pattern,
                                                     array_merge(array( '_controller' => $controller,
                                                                        '_page_id'    => $page->getId(),
                                                                        '_site_id'    => $page->getSite()->getId(),
                                                                        '_locale'     => $this->getLocale($pattern),
                                                                        '_domain'     => $domainDefault),
                                                                 $defaults),
                                                     $requirements,
                                                     array(),
                                                     $hostname
                                                ));
    }

    public function getLocale($pattern)
    {
        $patternArray = explode('/', $pattern);

        if (in_array($patternArray[1], Locale::getLocales())) {
            return $patternArray[1];
        }

        return $this->defaultLang;
    }

    /**
     * @param string $url
     * @return string
     */
    public function getBaseUrl($url)
    {
        if ($prepareUrl = str_replace("/{$this->getLocale($url)}", "/", $url)) {
            return $prepareUrl;
        }
        return "/";
    }

    /**
     * Returns true if this class supports the given type (db).
     *
     * @param mixed  $resource the name of a table with title and slug field 
     * @param string $type     The resource type (db)
     *
     * @return boolean True if this class supports the given type (db), false otherwise
     */
    public function supports($resource, $type = null)
    {
        return 'db' === $type;
    }

    /**
     * Returns stub controller string
     *
     * @return string
     */
    public function getController()
    {
        return 'ArmdCmsBundle:CatchAll:index';
    }
}