<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Services;

class ContainerCollectionService
{
    /**
     * @var array
     */
    protected $containersContant = array();

    /**
     * @param $containersContant
     */
    public function setContainersContant(array $containersContant)
    {
        $this->containersContant = $containersContant;
    }

    /**
     * @param $pageId
     * @param $containerName
     * @return array
     */
    public function getContainersContant($containerName)
    {
        if ( isset($this->containersContant[$containerName]) ) {
            return $this->containersContant[$containerName];
        }
        return false;
    }
}