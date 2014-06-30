<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\MainMenuBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

use Armd\MainMenuBundle\Model\TreeMenuInterface;

class MenuEnabledListener
{
    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $this->getEntity($args);
        $changes = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);

        if ($entity instanceof TreeMenuInterface && isset($changes['menuEnabled']) && !$changes['menuEnabled'][1]) {
            $args->getEntityManager()->createQueryBuilder()
                                     ->update(get_class($entity), 'mt')
                                     ->set('mt.menuEnabled', 'false')
                                     ->where('mt.lft between :left and :right')
                                     ->setParameter('left', $entity->getLvl())
                                     ->setParameter('right', $entity->getRgt())->getQuery()->execute();
        }
    }

    /**
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $args
     * @return \Armd\MainMenuBundle\Model\TreeMenuInterface
     */
    public function getEntity(LifecycleEventArgs $args)
    {
        return $args->getEntity();
    }
}