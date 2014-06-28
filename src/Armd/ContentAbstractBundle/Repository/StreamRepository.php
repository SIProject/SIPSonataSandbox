<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\ContentAbstractBundle\Repository;

class StreamRepository extends BaseContentRepository
{
    /**
     * @param null $entityName
     * @return array
     */
    public function findAll($entityName = null)
    {
        if (!$this->qb) {
            $this->createQueryBuilder('s');
        }

        if ($entityName) {
            $this->qb->leftJoin("{$this->alias}.entity", 'e')
                     ->andWhere('e.class = :class')
                     ->setParameter('class', $entityName);
        }

        return $this->getQuery()->getResult();
    }
}