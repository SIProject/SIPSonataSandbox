<?php
/*
 * (c) Isuhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\UsageType\Param;

use Symfony\Component\DependencyInjection\ContainerInterface;

class StreamParam extends ChoiceParam
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * @var array
     */
    protected $allowFields = array('class');

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return array
     */
    public function getChoiceList()
    {
        $choices = array();
        $qb = $this->doctrine->getManager()->createQueryBuilder('s')
                         ->select('s')->from('ArmdContentAbstractBundle:Stream', 's');
        if ($this->class) {
            $qb->join('s.entity', 'e')->where('e.class = :class')->setParameter('class', $this->class);
        }

        /** @var \Armd\ContentAbstractBundle\Entity\Stream[] $entities */
        if ($entities = $qb->getQuery()->getResult()) {
            foreach ($entities as $entity) {
                $choices[$entity->getId()] = $entity->getName();
            }
        }

        return $choices;
    }
}