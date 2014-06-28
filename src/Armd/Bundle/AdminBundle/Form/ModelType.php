<?php

namespace Armd\Bundle\AdminBundle\Form;

use Sonata\AdminBundle\Form\Type\ModelType as BaseModelType;

class ModelType extends BaseModelType
{
    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'genemu_jqueryselect2_choice';
    }
}