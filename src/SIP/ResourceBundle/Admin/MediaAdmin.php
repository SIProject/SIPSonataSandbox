<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\MediaBundle\Admin\ORM\MediaAdmin as BaseAdmin;

class MediaAdmin extends BaseAdmin
{
    /**
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            //->add('description')
            //->add('enabled')
            //->add('size')
        ;

        $listMapper->add('_action', 'actions', array('actions' => array(
            'edit' => array(),
            'delete' => array(),
        )));
    }
}