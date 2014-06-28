<?php

namespace Armd\ContentAbstractBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gedmo\Translatable\TranslatableListener;

class BaseContentRepository extends EntityRepository
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $qb;

    /**
     * @var string
     */    
    protected $alias;

    /**
     * @var array
     */
    protected $select = array();

    /**
     * @param $alias
     */
    public function setSelect($alias)
    {
        array_push($this->select, $alias);

        if (!in_array($this->getAlias(), $this->select)) {
            array_push($this->select, $this->getAlias());
        }

        $this->qb->select($this->select);
    }
    
    /**
     * {@inheritdoc}
     */
    public function createQueryBuilder($alias)
    {
        $this->select = array();
        $this->qb = parent::createQueryBuilder($alias);
        $this->alias = $alias;

        return $this->qb;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */        
    public function getQueryBuilder()
    {
        return $this->qb;
    }

    /**
     * @return \Doctrine\ORM\Query
     */            
    public function getQuery()
    {
        return $this->qb->getQuery();
    }

    /**
     * @param $id
     * @return array
     */
    public function findById($id)
    {
        if (!$this->qb) {
            $this->createQueryBuilder($this->alias);
        }

        return $this->setId($id)->getQuery()->getResult();
    }

    /**
     * @param $id
     * @return array
     */
    public function findOneById($id)
    {
        if (!$this->qb) {
            $this->createQueryBuilder($this->alias);
        }

        return $this->setId($id)->getQuery()->getOneOrNullResult();
    }
    
    /**
     * @param int $stream
     * @return BaseContentRepository     
     */        
    public function setStream($stream)
    {
        $this->qb
            ->andWhere("{$this->alias}.stream = :stream")
            ->setParameter('stream', $stream)
        ;
        
        return $this;
    }

    /**
     * @param array|int $id
     * @return BaseContentRepository
     */
    public function setId($id)
    {
        if (is_array($id) && empty($id)) {
            $id = 0;
        }

        if (is_array($id)) {
            $inClause = join(', ', $id);
            $whereClause = "{$this->alias}.id IN ({$inClause})";
        } else {
            $whereClause = "{$this->alias}.id = :id";
            $this->qb->setParameter('id', $id);
        }

        $this->qb->andWhere($whereClause);

        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @param bool $or
     * @return BaseContentRepository
     */
    public function setCriterion($name, $value, $or = false)
    {
        $or? $this->qb->orWhere("{$this->alias}." . $name . " = :" . $name):
             $this->qb->andWhere("{$this->alias}." . $name . " = :" . $name);

        $this->qb->setParameter($name, $value);

        return $this;
    }

    /**
     * @param string $sort
     * @param string $order
     * @return BaseContentRepository
     */
    public function setOrder($sort, $order = 'ASC')
    {
        $this->qb->orderBy("{$this->alias}.{$sort}", $order);

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Sets result cache and gets query
     *
     * @param integer $lifetime
     * @param string $resultCacheId
     * @param string $namespace
     * @return \Doctrine\ORM\AbstractQuery This query instance.
     */
    public function getQueryResultCache($lifetime = null, $resultCacheId = null)
    {
        return $this->getQuery()->useResultCache(true, $lifetime, $resultCacheId);
    }
}
