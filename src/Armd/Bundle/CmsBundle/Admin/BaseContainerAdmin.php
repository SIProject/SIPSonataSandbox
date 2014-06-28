<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Admin;

use Sonata\AdminBundle\Route\RouteCollection;

abstract class BaseContainerAdmin extends AwareContainerAdmin
{
    /**
     * @param \Sonata\AdminBundle\Route\RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection) {
        $collection->add('usageTypeAdd',
            'usageType');
        $collection->add('usageType',
            '{id}/usageType');
        $collection->add('usageTypeParamsAdd',
            'usageTypeParams');
        $collection->add('usageTypeParams',
            '{id}/usageTypeParams');
    }
}