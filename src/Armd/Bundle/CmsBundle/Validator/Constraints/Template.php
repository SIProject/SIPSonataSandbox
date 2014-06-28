<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Template extends Constraint
{
    public $message = 'Can not find template with name "%string%".';

    public function validatedBy()
    {
        return 'armd_cms.validator.template';
    }
}