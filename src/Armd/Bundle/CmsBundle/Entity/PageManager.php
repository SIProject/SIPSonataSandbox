<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Entity;

use Doctrine\ORM\EntityManager;

class PageManager
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $class;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param string $class
     */
    public function __construct(EntityManager $entityManager, $class = 'Armd\Bundle\CmsBundle\Entity\Page')
    {
        $this->em    = $entityManager;
        $this->class = $class;
    }

    /**
     * @param $routeName
     * @return Page
     */
    public function getPageByName($routeName)
    {
        return $this->findOneBy(array('routeName' => $routeName));
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Entity\PageRepository
     */
    protected function getRepository()
    {
        return $this->em->getRepository($this->class);
    }

    /**
     * return a page with the give slug
     *
     * @param string $url
     * @return \Armd\Bundle\CmsBundle\Entity\Page
     */
    public function getPageByUrl($url)
    {
        return $this->findOneBy(array('url' => $url));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->getRepository()->findOneById($id);
    }

    /**
     * @param array $criteria
     * @return \Armd\Bundle\CmsBundle\Entity\Page[]
     */
    public function findBy(array $criteria = array())
    {
        return $this->getRepository()->findBy($criteria, array('lft'=>'ASC'));
    }

    /**
     * @param array $criteria
     * @return \Armd\Bundle\CmsBundle\Entity\Page
     */
    public function findOneBy(array $criteria = array())
    {
        return $this->getRepository()->findOneBy($criteria, array('lft'=>'ASC'));
    }
}