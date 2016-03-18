<?php
namespace SIP\ResourceBundle\Admin;

use SIP\ResourceBundle\Entity\Page;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Admin\Admin;

class PageAdmin extends Admin
{
    protected function configureRoutes(RouteCollection $collection) {}

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('General')
                ->with('General')
                    ->add('title', 'text', ['label' => 'Заголовок'])
                    ->add('slug', 'text', ['label' => 'Адрес'])
                    ->add('body', 'textarea', ['label' => 'Контент', 'required' => false])
                ->end()
            ->end()
            ->tab('Meta')
                ->with('Meta')
                    ->add('metaTitle', null, array('label' => 'sip_meta_title', 'required' => false))
                    ->add('metaDescription', null, array('label' => 'sip_meta_description', 'required' => false))
                    ->add('metaKeywords', null, array('label' => 'sip_meta_keywords', 'required' => false))
                ->end()
            ->end()
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', 'text', ['label' => 'Заголовок'])
            ->add('slug', 'text', ['label' => 'Url'])
            ->add('_action', 'actions', [
                'label' => 'Действия',
                'actions' => [
                    'edit' => [],
                    'delete' => []
//                    'show' => []
                ]
            ])
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {}
}