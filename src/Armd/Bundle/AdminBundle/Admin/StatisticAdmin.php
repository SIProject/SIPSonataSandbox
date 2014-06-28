<?php

namespace Armd\Bundle\AdminBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Admin\Admin;

class StatisticAdmin extends Admin
{
    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('counterId');
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
                ->add('counterId')
                ->add('gaCounterId')
                ->add('providerClass', 'choice', array(
                    'choices' => array(
                        'YandexMetrika'   => 'Yandex.Metrika',
                        'GoogleAnalytics' => 'GoogleAnalytics'
                    )
                ))
                ->end()
            ->with('AccessData')
                ->add('appId')
                ->add('appSecret')
                ->add('userLogin')
                ->add('userPassword', 'password')
                ->add('isActive')
                ->end();
        
        parent::configureFormFields($formMapper);
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('counterId')
            ->add('isActive')
            ->add('providerClass');
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('counterId')
                       ->add('isActive')
                       ->add('providerClass');
    }
}
