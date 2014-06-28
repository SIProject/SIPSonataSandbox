<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Armd\Bundle\CmsBundle\Manager\PageManager;

abstract class BaseSelectType extends AbstractType
{
    /**
     * @var \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    protected $pm;

    /**
     * @param \Armd\Bundle\CmsBundle\Manager\PageManager $pm
     */
    public function __construct(PageManager $pm)
    {
        $this->pm = $pm;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->getChoices()
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return array();
    }
}