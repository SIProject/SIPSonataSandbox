<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Admin\Admin;

class RealEstateAdmin extends Admin
{
    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'dateUpload'  // name of the ordered field
    );

    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('show');
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
                ->add('description')
                ->add('uploadedImage', 'image_file_type', array('required' => false, 'attr' => array('path' => $this->getSubject()->getImage())))
                ->add('link', 'url')
            ->end()
            ->with('properties')
                ->add('location')
                ->add('city')
                ->add('distance')
                ->add('homeArea')
                ->add('pieceArea')
            ->end()
            ->with('price')
                ->add('price')
                ->add('currency', 'choice', array(
                    'choices' => array(
                        '1' => 'USD',
                        '2' => 'EUR',
                        '3' => 'RUB',
                    )
                ))
            ->end()
            ->with('system')
                ->add('dateUpload', 'genemu_jquerydate', array(
                    'widget' => 'single_text', 'required' => false
                ))
                ->add('dateUpdate', 'genemu_jquerydate', array(
                    'widget' => 'single_text', 'required' => false
                ))
            ->end()
        ;
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
            ->addIdentifier('title')
            ->addIdentifier('image', null, array('template' => 'SIPResourceBundle:Admin:image_form_type.html.twig'))
            ->add('price')
            ->add('location')
            /*->add('currency', null,
                array('template' => 'SIPResourceBundle:Admin:list_choice.html.twig',
                    'choices' => array(1 => 'USD', 2 => 'EUR', 3 => 'RUB')
                )
            )*/
            ->add('link', 'string', array('template' => 'SIPResourceBundle:Admin:list_link.html.twig'))
            ->add('dateUpload')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('location')
            ->add('dateUpload')
        ;
    }
}