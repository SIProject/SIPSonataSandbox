<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\NewsBundle\Repository;

use Armd\ContentAbstractBundle\Repository\BaseContentRepository;

class NewsRepository extends BaseContentRepository
{    
    /**
     * @param \DateTime $date
     * @return NewsRepository
     */
    function setBeginDate(\DateTime $date)
    {
        $this->qb
            ->andWhere("{$this->alias}.date >= :begin_date")
            ->setParameter('begin_date', $date)
        ;

        return $this;
    }

    /**
     * @param \DateTime $date
     * @return NewsRepository
     */
    function setEndDate(\DateTime $date)
    {
        $this->qb
            ->andWhere("{$this->alias}.date <= :end_date")
            ->setParameter('end_date', $date)
        ;
        
        return $this;        
    }

    /**
     * @param string $order
     * @return NewsRepository     
     */        
    function orderByDate($order = 'desc')
    {
        $this->qb->orderBy("{$this->alias}.date", $order);
        
        return $this;
    }
}
