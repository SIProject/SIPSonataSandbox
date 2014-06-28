<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs,
    Symfony\Component\DependencyInjection\ContainerInterface;

use Armd\Bundle\CmsBundle\Entity\Page,
    Armd\Bundle\CmsBundle\Entity\Container,
    Armd\Bundle\CmsBundle\Entity\PageContainer;

class PageUrlUpdateListener
{
    /**
     * @var ContainerInterface $container
     */
    protected $container;
    
    /**
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Page) {
            $this->updateUrl($entity);

            $classMetadate = $args->getEntityManager()->getClassMetadata(get_class($entity));
            $args->getEntityManager()->getUnitOfWork()->recomputeSingleEntityChangeSet($classMetadate, $entity);
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        if ($args->getEntity() instanceof Page) {
            $this->updateUrl($args->getEntity());
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        /** @var Page|Container|PageContainer $entity */
        $entity = $args->getEntity();
        $changes = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);

        if ($entity instanceof Page && isset($changes['slug'])) {
            if ($children = $this->getPageRepository($args)->getChildren($entity)) {
                foreach ($children as $child) {
                    $child = $this->updateUrl($child);
                    $args->getEntityManager()->persist($child);
                }
            }

            $args->getEntityManager()->flush();
            $this->recacheRoutesShutdown($args);
        } else if ($entity instanceof Container && $entity->getIsMain()) {
            $this->recacheRoutesShutdown($args);
        } else if ($entity instanceof PageContainer && $entity->getContainer()->getIsMain()) {
            $this->recacheRoutesShutdown($args);
        }
    }
    
    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        /** @var Page|Container|PageContainer $entity */
        $entity = $args->getEntity();

        if ($entity instanceof Page) {
            $this->recacheRoutesShutdown($args);
        }

        if ($entity instanceof Container && $entity->getIsMain()) {
            $this->recacheRoutesShutdown($args);
        }

        if( $entity instanceof PageContainer && $entity->getContainer()->getIsMain()) {
            $this->recacheRoutesShutdown($args);
        }
    }
    
    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        /** @var Page|Container|PageContainer $entity */
        $entity = $args->getEntity();

        if ($entity instanceof Page) {
            $this->recacheRoutesShutdown($args);
        }

        if ($entity instanceof Container && $entity->getIsMain()) {
            $this->recacheRoutesShutdown($args);
        }

        if ($entity instanceof PageContainer && $entity->getContainer()->getIsMain()) {
            $this->recacheRoutesShutdown($args);
        }
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\Page $page
     */
    public function updateUrl(Page $page)
    {
        if($page->getParent()) {
            if($page->getUrl() !== $page->getParent()->getUrl() . '/' . $page->getSlug()) {
                if($page->getParent()->getUrl() !== '/') {
                    $page->setUrl( $page->getParent()->getUrl() . '/' . $page->getSlug() );
                } else {
                    $page->setUrl( $page->getParent()->getUrl() . $page->getSlug() );
                }
            }
        } else {
            $page->setUrl('/' . $page->getSlug());
        }

        return $page;
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return \Armd\Bundle\CmsBundle\Entity\PageRepository
     */
    public function getPageRepository(LifecycleEventArgs $args)
    {
        return $args->getEntityManager()->getRepository('ArmdCmsBundle:Page');
    }
    
    public function recacheRoutesShutdown()
    {
        static $i;
        if(!$i){
            $i = 1;
            register_shutdown_function(array($this, 'recacheRoutes'));
        }
    }
    
    /**
     * Removes and creates routing cache for dev and prod environments
     * @return void
     */
    public function recacheRoutes()
    {
        $kernel = $this->container->get('kernel');
        $name   = $kernel->getName();
        $dir    = $kernel->getRootDir().'/cache/';
        $router = $this->container->get('router');
        
        // if $router->matcher and $router->generator are null, they'll be created and cached
        // so we need to nil them
        $o = new \ReflectionObject($router);
        $propM = $o->getProperty('matcher');
        $propG = $o->getProperty('generator');
        $propM->setAccessible(true);
        $propG->setAccessible(true);
        // hardcoded environments, 'cause can't dynamically get any except current
        $this->recacheRoutesInternal('prod', $dir, false, $name, $router, $propM, $propG);
        $this->recacheRoutesInternal('dev', $dir, true, $name, $router, $propM, $propG);
    }
    
    protected function recacheRoutesInternal($env, $dir, $debug, $name, $router, $propM, $propG)
    {
        $dir.= $env;
        $env = ucfirst($env);
        try {
            $this->container->get('filesystem')->remove(array(
                $dir.'/'.$name.$env.'UrlMatcher.php',
                $dir.'/'.$name.$env.'UrlMatcher.php.meta',
                $dir.'/'.$name.$env.'UrlGenerator.php',
                $dir.'/'.$name.$env.'UrlGenerator.php.meta',
            ));
        } catch (\Exception $ex) {}
        $propM->setValue($router, null);
        $propG->setValue($router, null);
        $router->setOption('generator_cache_class', $name.$env.'UrlGenerator');
        $router->setOption('matcher_cache_class', $name.$env.'UrlMatcher');
        $router->setOption('cache_dir', $dir);
        $router->setOption('debug', $debug);
        try {
            // will always cause fatal because classes are loaded twice
            // as we recache on shutdown, it doesn't matter and can be silently caught
            $router->warmUp($dir);
        } catch (\Exception $ex) {}
    }
}