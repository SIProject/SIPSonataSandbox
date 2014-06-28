<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainerMain extends Constraint
{
    public $message = 'This pageType already have main container! PageType can have only one main container.';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'armd_cms.validator.container_main';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}