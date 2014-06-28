<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainerUnique extends Constraint
{
    public $message = 'Duplicate container with area "%area%"';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'armd_cms.validator.container_unique';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}