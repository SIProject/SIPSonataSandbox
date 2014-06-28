<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PageUrlUnique extends Constraint
{
    public $message = 'Page with this url {{ string }} already exist. Edit the slug field!';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'armd_cms.validator.page_url_unique';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}