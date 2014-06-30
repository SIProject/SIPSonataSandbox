<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\NewsBundle\Controller;

use Armd\ContentAbstractBundle\Controller\Controller;

class NewsController extends Controller
{
    /**
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function listAction($page)
    {
        $query = $this->getListRepository()->getQuery();
        $entities = $this->getParamsValue('pagination')?
            $this->getPagination($query, $page): $query->setMaxResults($this->getParamsValue('per_page'))->getResult();

        return $this->renderCms(array('entities' => $entities));
    }

    /**
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function itemAction($id)
    {
        $entity = $this->getEntityRepository()->setId($id)->getQueryResultCache(null, $this->getEntityCacheKey($id))->getOneOrNullResult();

        if (null === $entity) {
            throw $this->createNotFoundException(sprintf('Unable to find record %d', $id));
        }

        return $this->renderCms(array('entity' => $entity));
    }

    /**
     * @param string $from
     * @param string $to
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function archiveAction($from, $to, $page)
    {
        return $this->renderCms(array('entities' => $this->getPagination($this->getArchiveRepository($from, $to)->getQuery(), $page)));
    }

    /**
     * @param string $from
     * @param string $to
     * @return \Armd\ContentAbstractBundle\Repository\BaseContentRepository
     */
    function getArchiveRepository($from, $to)
    {
        $format = 'd.m.Y';
        $date = $to ? \DateTime::createFromFormat($format, $to) : new \DateTime();

        $repository = $this->getListRepository()->setEndDate($date);

        return $from ? $repository->setBeginDate(\DateTime::createFromFormat($format, $from)) : $repository;
    }

    /**
     * @return \Armd\NewsBundle\Repository\NewsRepository
     */
    function getListRepository()
    {
        $repository = $this->getEntityRepository();
        $stream = $this->getStream();
        $repository->setEndDate(new \DateTime())->orderByDate();
        return $stream ? $repository->setStream($stream) : $repository;
    }

    /**
     * @return integer
     */
    function getStream()
    {
        return $this->getParamsValue('content_stream', 0);
    }
}
