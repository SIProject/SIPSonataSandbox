<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Admin;

use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

class PageContainerAdmin extends BaseContainerAdmin
{
    protected function configureRoutes(RouteCollection $collection) {
        parent::configureRoutes($collection);
        $collection->add('createPageContainer',
            'create/{containerId}/{pageId}');
    }

    /**
     * @param \Sonata\AdminBundle\Show\ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowField(ShowMapper $showMapper)
    {
        $showMapper
            ->add('page')
            ->add('container')
            ->add('usageService')
            ->add('usageType')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $pageQueryBuilder = $this->getPageRepository()->createQueryBuilder('p')->orderBy('p.root, p.lft, p.lvl');

        $formMapper
            ->with('General')
                ->add('usageService', 'selectUsageService',
                    array('required' => false, 'configs' => array('allowClear' => true)))
                ->add('usageType', 'selectUsageType',
                    array('required' => false, 'configs' => array('allowClear' => true)))
                ->add('settings', 'usageParamsType', array('required' => false))
                ->add('page', 'genemu_jqueryselect2_entity',
                    array('class'    => 'Armd\Bundle\CmsBundle\Entity\Page',
                          'property' => 'title', 'query_builder' => $pageQueryBuilder))
                ->add('container', 'genemu_jqueryselect2_entity',
                    array('class'    => 'Armd\Bundle\CmsBundle\Entity\Container',
                          'property' => 'toString'))
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
            ->add('container')
            ->add('page')
            ->add('usageService')
            ->add('usageType')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $datagrid
     */
    protected function configureDatagridFilters(DatagridMapper $datagrid)
    {
        $datagrid
            ->add('page')
            ->add('container');
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Entity\PageRepository
     */
    public function getPageRepository()
    {
        return $this->container->get('doctrine.orm.entity_manager')->getRepository('ArmdCmsBundle:Page');
    }
}