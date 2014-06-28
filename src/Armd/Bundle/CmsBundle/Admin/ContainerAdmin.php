<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class ContainerAdmin extends BaseContainerAdmin
{
    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('area')
            ->add('usageType')
            ->add('usageService')
            ->add('pageType')
            ->add('is_main')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('title')
                ->add('area', 'sonata_type_model')
                ->add('usageService', 'selectUsageService',
                    array('required' => false, 'configs' => array('allowClear' => true)))
                ->add('usageType', 'selectUsageType',
                    array('required' => false, 'configs' => array('allowClear' => true)))
                ->add('settings', 'usageParamsType', array('required' => false))
                ->add('is_main')
                ->add('pageType', 'genemu_jqueryselect2_entity', array('class'    => 'Armd\Bundle\CmsBundle\Entity\PageType',
                                                                       'property' => 'title',
                                                                       'multiple' => true,
                                                                       'required' => false,
                                                                       'by_reference' => false))
            ->end();
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title')
            ->add('area')
            ->add('usageType')
            ->add('usageService')
            ->add('pageType')
            ->add('is_main')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagrid
     */
    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('pageType', null, array(), null, array('expanded' => true, 'multiple' => true, 'required' => false))
            ->add('area')
            ->add('title')
            ->add('is_main');
    }
}