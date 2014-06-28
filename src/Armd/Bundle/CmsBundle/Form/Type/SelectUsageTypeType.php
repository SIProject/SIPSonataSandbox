<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class SelectUsageTypeType extends BaseSelectType
{
    /**
     * @return array
     */
    public function getChoices()
    {
        $choices = array();

        foreach ($this->pm->getUsageServices() as $service) {
            foreach ($service->getContainerTypes() as $type) {
                $choices[$type->getName()] = $type->getName();
            }
        }

        return $choices;
    }

    /**
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $class = isset($view->vars['attr']['class'])? $view->vars['attr']['class'] . ' ': '';
        $view->vars['attr']['class'] = $class . 'selectUsageType';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'selectUsageType';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'genemu_jqueryselect2_choice';
    }
}