<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AwareContainerAdmin extends Admin
{
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
}