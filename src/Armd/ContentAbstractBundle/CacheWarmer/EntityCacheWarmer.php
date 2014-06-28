<?php

namespace Armd\ContentAbstractBundle\CacheWarmer;

use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

use Armd\ContentAbstractBundle\Util\ServicesPool;
use Armd\ContentAbstractBundle\Entity\Entity;

class EntityCacheWarmer implements CacheWarmerInterface
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine.orm.entity_manager');    
    }
    
    /**
     * {@inheritdoc}
     */    
    public function isOptional()
    {
        return true;
    }        
    
    /**
     * {@inheritdoc}
     */    
    public function warmUp($cacheDir)
    {
        $isEntityPersisted = false;

        foreach($this->container->get('armd.content.pool')->getAdminServiceIds() as $id) {
        
            $admin = $this->container->get($id);

            if (!$this->isEntityExists($id)) {
                $this->createEntity($admin->getLabel(), $admin->getClass(), $id);
                $isEntityPersisted = true;
            }            
        }
        
        if ($isEntityPersisted) {
            $this->em->flush();
        }
    }
    
    /**
     * @param  string $serviceId
     * @return boolean
     */    
    protected function isEntityExists($serviceId)
    {
        $repository = $this->em->getRepository('ArmdContentAbstractBundle:Entity');
        $entity = $repository->findOneByService($serviceId);
        
        return null !== $entity;                
    }

    /**
     * @param  string $classname
     * @return Armd\ContentAbstractBundle\Entity\Entity
     */        
    protected function createEntity($name, $classname, $serviceId)
    {
        $entity = new Entity();
        $entity
            ->setName($name)
            ->setClass($classname)
            ->setService($serviceId);
        
        $this->em->persist($entity);
        
        return $entity;
    }            
}

?>