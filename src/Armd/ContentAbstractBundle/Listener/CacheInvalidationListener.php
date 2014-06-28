<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\ContentAbstractBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Armd\ContentAbstractBundle\Cache\CacheInvalidator;
use Armd\ContentAbstractBundle\Model\ResultCacheableInterface;

class CacheInvalidationListener
{
    /**
     * @var \Armd\ContentAbstractBundle\Cache\CacheInvalidator
     */
    protected $cacheInvalidator;

    /**
     * @param \Armd\ContentAbstractBundle\Cache\CacheInvalidator $cacheInvalidator
     */
    public function __construct($cacheInvalidator)
    {
        $this->cacheInvalidator = $cacheInvalidator;
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        if (($object = $args->getEntity()) instanceof ResultCacheableInterface) {
            $this->cacheInvalidator->invalidationCache($object);
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        if (($object = $args->getEntity()) instanceof ResultCacheableInterface) {
            $this->cacheInvalidator->invalidationCache($object);
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        if (($object = $args->getEntity()) instanceof ResultCacheableInterface) {
            $this->cacheInvalidator->invalidationCache($object);
        }
    }
}