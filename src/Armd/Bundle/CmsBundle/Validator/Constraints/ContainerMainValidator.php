<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainerMainValidator extends ConstraintValidator
{
    /**
     * @var \Doctrine\Bundle\DoctrineBundle\Registry
     */
    protected $doctrine;

    /**
     * @param \Doctrine\Bundle\DoctrineBundle\Registry $doctrine
     */
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\Container $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value->getIsMain()) {
            $pageTypes = array();
            foreach ($value->getPageType() as $pageType) {
                $pageTypes[] = $pageType->getId();
            }
            $qb = $this->doctrine->getManager()->createQueryBuilder('c');
            $qb->select($qb->expr()->count('c'))->from('ArmdCmsBundle:Container', 'c')
                ->innerJoin('c.pageType', 'pt')
                ->andWhere($qb->expr()->in('pt.id', $pageTypes))
                ->andWhere('c.is_main = :is_main')->setParameter('is_main', true);

            if ($value->getId()) {
                $qb->andWhere('c.id <> :id')->setParameter('id', $value->getId());
            }

            if (!empty($pageTypes) && $qb->getQuery()->getSingleScalarResult()) {
                $this->context->addViolation($constraint->message, array('%area%' => $value->getArea()->getTitle()));
            }
        }
    }
}