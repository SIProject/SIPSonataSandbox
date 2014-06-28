<?php
    /*
    * (c) Suhinin Ilja <isuhinin@armd.ru>
    */
namespace Armd\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PageUrlUniqueValidator extends ConstraintValidator
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
     * @param \Armd\Bundle\CmsBundle\Entity\Page $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $url = $this->updateUrl($value);
        $result = $this->doctrine->getManager()->getRepository(get_class($value))->findBy(array('url' => $url));

        if (count($result) === 0 || (count($result) === 1 && $result[0]->getId() == $value->getId()) ) {
            return;
        }

        $this->context->addViolation($constraint->message, array('{{ string }}' => $url));
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\Page $page
     */
    public function updateUrl(\Armd\Bundle\CmsBundle\Entity\Page $page)
    {
        if($page->getParent()) {
            if($page->getUrl() !== $page->getParent()->getUrl() . '/' . $page->getSlug()) {
                if($page->getParent()->getUrl() !== '/') {
                    return $page->getParent()->getUrl() . '/' . $page->getSlug();
                } else {
                    return $page->getParent()->getUrl() . $page->getSlug();
                }
            }
        }

        return '/' . $page->getSlug();
    }
}