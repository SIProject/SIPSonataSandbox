<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

/**
 * PageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PageRepository extends NestedTreeRepository
{
    /**
     * @param $id
     * @return \Armd\Bundle\CmsBundle\Entity\Page
     */
    public function findOneById($id)
    {
        $query = $this->getEntityManager()->createQuery(
            'SELECT p, pc, t, ptc, a, s
             FROM ArmdCmsBundle:Page p
                JOIN p.site s
                JOIN p.pageType t
                JOIN t.containers ptc
                JOIN ptc.area a
                LEFT JOIN p.pageContainers pc WITH pc.container = ptc.id
             WHERE p.id = :id')
            ->setParameter('id', $id);

        return $query->useResultCache(true, null, '_cms_page_' . $id)->getSingleResult();
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Entity\Page[]
     */
    public function findAllByMainContainer()
    {
        return $this->getEntityManager()->createQuery(
            'SELECT p, pc, t, ptc, s
             FROM ArmdCmsBundle:Page p
                JOIN p.site s
                JOIN p.pageType t
                JOIN t.containers ptc
                LEFT JOIN p.pageContainers pc WITH pc.container = ptc.id
             WHERE ptc.is_main = :is_main')
            ->setParameter('is_main', 1)
            ->getResult();
    }
}