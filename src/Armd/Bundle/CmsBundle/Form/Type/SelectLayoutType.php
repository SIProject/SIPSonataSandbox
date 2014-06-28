<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Form\Type;

class SelectLayoutType extends BaseSelectType
{
    public function getChoices()
    {
        $choices = array();

        foreach ($this->pm->getTemplateService()->getLayouts() as $layout) {
            $choices[$layout->getName()] = "{$layout->getTitle()} ({$layout->getName()})";
        }

        return $choices;
    }

    public function getName()
    {
        return 'selectLayout';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'genemu_jqueryselect2_choice';
    }
}