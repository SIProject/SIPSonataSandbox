<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Armd\Bundle\CmsBundle\Form\Type\KeyValueType;

class PageAdmin extends BaseTreeAdmin
{
    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection) {
        parent::configureRoutes($collection);

        $collection->add('list', 'list',
            array('_controller' => $this->baseTreeController . ':list'));
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
     * Returns the list template
     *
     * @return string the list template
     */
    public function getTreeTemplate()
    {
        return 'ArmdCmsBundle:Admin:tree.html.twig';
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
            ->add('slug')
            ->add('url')
            ->add('containers')
            ->add('parent')
            ->add('pageType')
            ->add('menuEnabled')
            ->add('toFirstChild')
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
        $parentQueryBuilder = $this->getPageRepository()->createQueryBuilder('p')->orderBy('p.root, p.lft, p.lvl');

        $formMapper
            ->with('General')
                ->add('title')
                ->add('slug')
                ->add('parent', 'genemu_jqueryselect2_entity',
                    array('class' => 'Armd\Bundle\CmsBundle\Entity\Page',
                          'property' => 'title',
                          'required' => false,
                          'query_builder' => $parentQueryBuilder))
                ->add('pageType', 'genemu_jqueryselect2_entity',
                    array('class' => 'Armd\Bundle\CmsBundle\Entity\PageType',
                          'property' => 'title'))
                ->add('site', 'genemu_jqueryselect2_entity',
                    array('class' => 'Armd\Bundle\CmsBundle\Entity\Site',
                          'property' => 'title'))
                ->add('pageContainers', null, array('required' => false, 'attr' => array('class' => 'hidden')), array('edit' => 'list', 'reload' => true))
                ->add('menuEnabled')
                ->add('toFirstChild', null, array('required' => false))
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
            ->add('url')
            ->add('slug')
            ->add('containers')
            ->add('parent')
            ->add('pageType')
            ->add('menuEnabled')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\DatagridMapper $filterMapper
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('containers')
            ->add('pageType')
            ->add('menuEnabled')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Validator\ErrorElement $errorElement
     * @param $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        if (!preg_match('/^[a-z0-9A-Z_.\-]+$/', $object->getSlug())) {
            $errorElement->with('slug')->addViolation('Недопустимое значение псевдонима')->end();
        }
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Entity\PageRepository
     */
    public function getPageRepository()
    {
        return $this->container->get('doctrine.orm.entity_manager')->getRepository('ArmdCmsBundle:Page');
    }
}