<?php

namespace Armd\ContentAbstractBundle\Util;

class ServicesPool
{
    protected $adminServiceIds = array();
    
    /**
     * @param array $adminServiceIds
     *
     * @return void
     */
    public function setAdminServiceIds(array $adminServiceIds)
    {
        $this->adminServiceIds = $adminServiceIds;
    }

    /**
     * @return array
     */
    public function getAdminServiceIds()
    {
        return $this->adminServiceIds;
    }    
}
