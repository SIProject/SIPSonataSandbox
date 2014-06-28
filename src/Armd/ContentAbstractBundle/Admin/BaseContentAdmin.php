<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\ContentAbstractBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BaseContentAdmin extends Admin
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_by' => 'id',
        '_sort_order' => 'DESC',
    );

    /**
     * The class name managed by the admin class
     *
     * @var ContainerInterface $container
     */
    protected $container;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     */
    public function __construct($code, $class, $baseControllerName, $container)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->container = $container;
    }

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('_action', 'actions', array('actions' => array(
                'view' => array(),
                'edit' => array(),
                'delete' => array(),
            )))
        ;
    }

    public function getEntityStream()
    {
        return $this->getEntityManager()
                    ->createQuery('SELECT s
                                   FROM ArmdContentAbstractBundle:Stream s JOIN s.entity ce
                                   WHERE ce.class = :entity')
                    ->setParameter('entity', $this->getClass());
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (!$this->em) {
            $this->em = $this->container->get('doctrine.orm.entity_manager');
        }

        return $this->em;
    }

    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     * @return void
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);

        $collection->add('history_revert_revision', $this->getRouterIdParameter() . '/history/{revision}/revert');
    }
}