<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class KeyValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('key', null, array('label' => 'key'));
        $builder->add('value', 'textarea', array('required' => false, 'label' => 'value'));
    }

    public function getName()
    {
        return 'key_value';
    }
}