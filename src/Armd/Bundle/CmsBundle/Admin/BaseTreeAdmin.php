<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Route\RouteCollection;

use Armd\ContentAbstractBundle\Admin\BaseContentAdmin;

abstract class BaseTreeAdmin extends BaseContentAdmin
{
    protected $baseTreeController = 'ArmdCmsBundle:BaseTree';

    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection) {
        parent::configureRoutes($collection);

        $collection->add('sublist', 'sublist',
            array('_controller' => $this->baseTreeController . ':sublist'));
        $collection->add('copy', '{id}/copy',
            array('_controller' => $this->baseTreeController . ':copy'));
        $collection->add('createchild', '{id}/createchild',
            array('_controller' => $this->baseTreeController . ':createchild'));
        $collection->add('movePageNode', 'movePageNode',
            array('_controller' => $this->baseTreeController . ':movePageNode'));
    }
}