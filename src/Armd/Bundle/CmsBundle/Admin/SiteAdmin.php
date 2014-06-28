<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

use Armd\Bundle\CmsBundle\Form\Type\KeyValueType;

class SiteAdmin extends Admin
{
    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('domains')
            ->add('logo', null, array('template'=>'ArmdCmsBundle:Admin:show_image.html.twig'))
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
                ->add('logo', 'genemu_jqueryimage')
                ->add('parameters', 'collection',
                    array('type' => new KeyValueType(),
                          'allow_add' => true,
                          'allow_delete' => true,
                          'by_reference' => false,
                          'required' => false))
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
            ->addIdentifier('title')
            ->add('domains')
            ->add('logo', null, array('template'=>'ArmdCmsBundle:Admin:list_image.html.twig'));
    }
}