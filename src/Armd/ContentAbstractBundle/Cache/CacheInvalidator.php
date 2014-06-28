<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\ContentAbstractBundle\Cache;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Armd\ContentAbstractBundle\Model\ResultCacheableInterface;

class CacheInvalidator
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    protected $cache;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param \Armd\ContentAbstractBundle\Model\ResultCacheableInterface $object
     */
    public function  invalidationCache(ResultCacheableInterface $object)
    {
        if(is_array($keys = $this->getEntityCacheKeys($object))) {
            foreach($keys as $key) {
                $this->getCache()->delete($key);
            }
        }
    }

    /**
     * @param \Armd\ContentAbstractBundle\Model\ResultCacheableInterface $object
     * @return array
     */
    public function getEntityCacheKeys(ResultCacheableInterface $object)
    {
        $keys = array();
        foreach ($this->getCacheIdentifiers($object) as $cacheIdentifier) {
            if (strpos($cacheIdentifier, '_') === 0) {
                $keys[] = $cacheIdentifier; continue;
            }

            $key = get_class($object) . '_item_' . $cacheIdentifier;
            try {
                $locales = $this->container->getParameter('locale_list');
            } catch(\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException $ex) {
                $locales = array('ru', 'en');
            }

            foreach ($locales as $locale) {
                $keys[] = $key . '_' . $locale;
            }
        }

        return $keys;
    }

    /**
     * @param \Armd\ContentAbstractBundle\Model\ResultCacheableInterface $object
     * @return array
     */
    public function getCacheIdentifiers(ResultCacheableInterface $object)
    {
        $cacheIdentifiers = array();
        foreach ($object->getCacheKeys() as $cacheKey) {
            $methodName = 'get' . ucfirst($cacheKey);
            if ($cacheKey && method_exists($object, $methodName)) {
                $cacheIdentifiers[] = $object->$methodName();
            } else {
                $cacheIdentifiers[] = $cacheKey;
            }
        }

        return $cacheIdentifiers;
    }

    /**
     * @return \Doctrine\Common\Cache\Cache
     */
    public function getCache()
    {
        if (!$this->cache) {
            $this->cache = $this->container->get('doctrine')->getManager()->getConfiguration()->getResultCacheImpl();
        }

        return $this->cache;
    }
}