<?php

namespace Armd\Bundle\CmsBundle\UsageType\Param;

use Symfony\Component\DependencyInjection\ContainerInterface;

class RoleParam extends ChoiceParam
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getChoiceList()
    {
        $roles = array();
        foreach ($this->container->getParameter('security.role_hierarchy.roles') as $roleKey => $role) {
            if (!in_array($roleKey, $roles)) {
                $roles[$roleKey] = $roleKey;
            }

            foreach ($role as $component) {
                if (!in_array($component, $roles)) {
                    $roles[$component] = $component;
                }
            }
        }
        return $roles;
    }
}