<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Validator\ErrorElement;

class PageTypeAdmin extends AwareContainerAdmin
{
    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('create_container');

        $collection->add('copy', '{id}/copy', array('_controller' => 'ArmdCmsBundle:PageType:copy'));

        //$this->getRoute('create_container')->setPattern($this->container->get('armd_cms.admin.container')->getRoute('create')->getPattern());
    }
    /**
     * Returns the list template
     *
     * @return string the list template
     */
    public function getBlocksTemplate()
    {
        return 'ArmdCmsBundle:Admin:blocks.html.twig';
    }

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('title')
            ->add('layout')
            ->add('page')
            ->add('containers')
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
                ->add('layout', 'selectLayout')
                ->add('containers', 'genemu_jqueryselect2_entity', array('class'    => 'Armd\Bundle\CmsBundle\Entity\Container',
                                                                         'property' => 'toString',
                                                                         'multiple' => true,
                                                                         'required' => false), array('reload' => true))
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
            ->add('layout')
            ->add('page')
            ->add('containers')
            ->add('_action', 'actions',
                array('actions' =>
                    array('view'   => array(),
                          'edit'   => array(),
                          'delete' => array(),
                          'copy'   => array('template' => 'ArmdCmsBundle:Admin:action_copy.html.twig'),
                          )
                )
            )
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filterMapper
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('containers')
            ->add('page')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Validator\ErrorElement $errorElement
     * @param $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        /** @var \Armd\Bundle\CmsBundle\Services\TemplateService $themeService */
        $themeService = $this->container->get('armd_cms.page_manager')->getTemplateService();
        $layout = $themeService->getLayout($object->getLayout());

        $templating = $this->container->get('templating');
        if (!$templating->exists($layout->getLayoutPath())) {
            $errorElement
                ->with('layout')
                    ->addViolation('Файл ' . $layout . ' не существует!')
                ->end();
        }

        $blockGrid = $themeService->getLayout($object->getLayout())->getBlocksGridPath();
        if (!$templating->exists($layout->getBlocksGridPath())) {
            $errorElement
                ->with('layout')
                    ->addViolation('Файл ' . $blockGrid . ' не существует!')
                ->end();
        }
    }
}