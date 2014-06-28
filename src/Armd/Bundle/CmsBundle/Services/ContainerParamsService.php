<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Services;

class ContainerParamsService
{
    /**
     * @var string
     */
    protected $usageServiceName;

    /**
     * @var string
     */
    protected $usageTypeContainerName;

    /**
     * @var array
     */
    protected $containerRequestParams;

    /**
     * @var array
     */
    protected $containerResponseParams;

    /**
     * @param string $usageServiceName
     */
    public function setUsageServiceName($usageServiceName)
    {
        $this->usageServiceName = $usageServiceName;
    }

    /**
     * @return string
     */
    public function getUsageServiceName()
    {
        return $this->usageServiceName;
    }

    /**
     * @param string $usageTypeContainerName
     */
    public function setUsageTypeContainerName($usageTypeContainerName)
    {
        $this->usageTypeContainerName = $usageTypeContainerName;
    }

    /**
     * @return string
     */
    public function getUsageTypeContainerName()
    {
        return $this->usageTypeContainerName;
    }

    /**
     * @param array $params
     */
    public function setContainerRequestParams(array $params = array())
    {
        $this->containerRequestParams = $params;
    }

    /**
     * @param array $params
     */
    public function setContainerResponseParams(array $params = array())
    {
        $this->containerResponseParams = $params;
    }

    /**
     * @return array
     */
    public function getContainerRequestParams()
    {
        return $this->containerRequestParams;
    }

    /**
     * @return array
     */
    public function getContainerResponseParams()
    {
        return $this->containerResponseParams;
    }
}