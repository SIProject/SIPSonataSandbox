<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\UsageType;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

use Armd\Bundle\CmsBundle\Entity\BaseContainer;
use Armd\Bundle\CmsBundle\UsageType\Param\BaseParam;

class BaseUsageService
{
    /**
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $adminId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $bundleName;

    /**
     * @var array
     */
    protected $usageTypeDef = array();

    /**
     * @var array
     */
    protected $params = array();

    /**
     * @var \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer[]
     */
    protected $types = array();

    /**
     * @var array
     */
    protected $typeFieldsReq = array('controller', 'action');

    /**
     * @var array
     */
    protected $typeFieldsOptional = array('params', 'route', 'title');

    /**
     * @var array
     */
    protected $groupFieldsReq = array('types');

    /**
     * @var array
     */
    protected $groupFieldsOptional = array('params');

    /**
     * @var array
     */
    protected $arrayDefOptional = array('params');

    /**
     * @var array
     */
    protected $usageTypes = array();

    /**
     * @param $name
     * @param array $usageTypeDef
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param string $bundleName
     */
    public function __construct($name, array $usageTypeDef, ContainerInterface $container, $bundleName = 'ArmdResourceBundle')
    {
        $this->name         = $name;
        $this->usageTypeDef = $usageTypeDef;
        $this->bundleName   = $bundleName;
        $this->container    = $container;
        $this->initTypes();
    }

    /**
     * @return string
     */
    public function getBundleName()
    {
        return $this->bundleName;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\UsageType\BaseParam[]
     */
    public function getParams()
    {
        return $this->params;
    }

    public function getParamsValue()
    {
        $params = array();
        foreach ($this->getParams() as $param) {
            $params[$param->getTitle()] = $param->getValue();
        }

        return $params;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasParam($name)
    {
        return isset($this->params[$name]);
    }

    /**
     * @param string $name
     * @return \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam
     */
    public function getParam($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : new Param\NullParam;
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam[] $params
     */
    public function setParams(array $params)
    {
        $this->params = array();
        foreach($params as $param) {
            $this->addParam($param);
        }
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam $param
     */
    public function addParam(Param\BaseParam $param) {
        $this->params[$param->getName()] = $param;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function initTypes()
    {
        $this->validateMainDef();

        if ( isset($this->usageTypeDef['params']) ) {
            foreach ( $this->usageTypeDef['params'] as $paramName => $bundleParam ) {
                $this->addParam($this->initParam($bundleParam, $paramName));
            }
        }

        $this->usageTypes = $this->parseUsageTypes();

        foreach($this->usageTypeDef['group'] as $groupName => $groupDef) {
            $this->validateUsageGroupDef($groupName, $groupDef);
            $containerUsage = new UsageTypeContainer();
            $containerUsage->setName($groupName);
            foreach($groupDef['types'] as $usageTypeName) {
                if (!isset($this->usageTypes[$usageTypeName])) {
                    throw new \InvalidArgumentException( sprintf( "UsageType named '%s' not defined", $usageTypeName) );
                }
                $containerUsage->addType($this->usageTypes[$usageTypeName]);
            }
            if ( isset($groupDef['params']) ) {
                foreach ( $groupDef['params'] as $paramName => $groupParam ) {
                    $containerUsage->addParam($this->initParam($groupParam, $paramName));
                }
            }
            $containerUsage->addParam($this->getTemplateParam());

            $this->addContainerType($containerUsage);
        }
    }

    /**
     * @param array $param
     * @param $paramName
     * @return Param\BaseParam
     */
    public function initParam(array $param, $paramName)
    {
        $type = isset($param['type']) ? $param['type'] : 'string';
        $paramType = $this->getParamTypeObject($type);

        $paramType->validateParamDef($param);
        $paramType->setName($paramName)->fromArray($param)->setModuleName($this->name);

        return $paramType;
    }

    /**
     * Extract usage types from definition
     *
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageType[]
     * @throws \InvalidArgumentException
     */
    public function parseUsageTypes()
    {
        $result = array();
        foreach ($this->usageTypeDef['types'] as $typeName => $typeDef) {
            $usageType = new UsageType();
            $usageType->setName($typeName);
            $usageType->setModuleName($this->name);
            $this->parseUsageType($usageType, $typeDef);
            if (isset($result[$typeName])) {
                throw new \InvalidArgumentException(
                    sprintf('UsageType name must be unique for module (find more than one named "%s")', $typeName)
                );
            }
            $result[$typeName] = $usageType;
        }
        return $result;
    }

    /**
     * Fill UsageType with UsageType/Params by array defention
     *
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageType $usageType
     * @param array $usageTypeDef
     */
    public function parseUsageType(UsageType $usageType, array $usageTypeDef)
    {
        $this->validateUsageTypeDef($usageType->getName(), $usageTypeDef);
        foreach($this->typeFieldsReq as $field) {
            $setMethod = 'set' . ucfirst($field);
            $usageType->$setMethod($usageTypeDef[$field]);
        }
        if (isset($usageTypeDef['route'])) {
            $routeDef = $usageTypeDef['route'];
            $defaults = isset($routeDef['defaults']) ? $routeDef['defaults'] : array();
            $requirements = isset($routeDef['requirements']) ? $routeDef['requirements'] : array();
            $route = new Route($routeDef['pattern'], $defaults, $requirements);
            $usageType->setRoute($route);
        }
        if (isset($usageTypeDef['params'])) {
            foreach($usageTypeDef['params'] as $paramName => $param) {
                $usageType->addParam($this->initParam($param, $paramName));
            }
        }
    }

    /**
     * @return Param\BaseParam
     */
    public function getTemplateParam()
    {
        return $this->initParam(array('type' => 'template', 'title' => 'Шаблон'), 'template');
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer $container
     */
    public function addContainerType(UsageTypeContainer $container) {
        $this->types[$container->getName()] = $container;
    }

    /**
     * Get list of possible usage types
     *
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer[]
     */
    public function getContainerTypes() {
        return $this->types;
    }

    /**
     * Return type container by name
     *
     * @param string $name
     * @throws \LogicException
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer
     */
    public function getContainerType($name)
    {
        if (!isset($this->types[$name])) {
            throw new \LogicException('Usage type named: ' . $name . ' not found');
        }
        return $this->types[$name];
    }

    /**
     * Return root name service
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fill UsateType with params stored in Container
     *
     * @param UsageType $usageType
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer $usageContainer
     * @param array $settings
     * @internal param $usageServiceName
     * @return bool
     */
    public function paramsBuild(UsageType $usageType, UsageTypeContainer $usageContainer, array $settings )
    {
        if ($this->getParams()) {
            $this->setParamValue($this->getParams(), $usageType, $settings);
        }

        if ($usageContainer->getParams()) {
            $this->setParamValue($usageContainer->getParams(), $usageType, $settings);
        }

        if ($usageType->getParams()) {
            $this->setParamValue($usageType->getParams(), $usageType, $settings);
        }
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam[] $params
     * @param UsageType $usageType
     * @param array $settings
     */
    public function setParamValue(array $params, UsageType $usageType, array $settings)
    {
        foreach($params as $param) {
            if (isset($settings[$param->getName()])) {
                $param->setValue($settings[$param->getName()]);
            }
            $usageType->addParam($param);
        }
    }

    /**
     * @param UsageType $usageType
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer $usageContainer
     * @param \Armd\Bundle\CmsBundle\Entity\BaseContainer|\Armd\Bundle\CmsBundle\Entity\PageContainer|null $container
     * @internal param \Armd\Bundle\CmsBundle\Entity\Container $typeContainer
     * @return array
     */
    public function getRealParams(UsageType $usageType, UsageTypeContainer $usageContainer, BaseContainer $container) {
        $params = array();

        if (is_array($this->getParams())) {
            foreach ($this->getParams() as $usageServiceParamName => $usageServiceParam) {
                if ($container->hasSetting($usageServiceParamName)) {
                    $params[$usageServiceParamName] = $container->getSetting($usageServiceParamName);
                }
            }
        }

        if (is_array($usageContainer->getParams())) {
            foreach ($usageContainer->getParams() as $usageTypeContainerName => $usageTypeContainer) {
                $params[$usageTypeContainerName] = $container->getSetting($usageTypeContainerName);
            }
        }

        if (is_array($usageType->getParams())) {
            foreach ($usageType->getParams() as $usageTypeParamName => $usageTypeParam) {
                $params[$usageTypeParamName] = $container->getSetting($usageTypeParamName);
            }
        }

        return $params;
    }

    /**
     * @param $type
     * @return \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam
     * @throws \LogicException
     */
    public function getParamTypeObject($type)
    {
        $params = $this->container->getParameter('armd_cms.usage_param.params');

        if (!isset($params[$type])) {
            throw new \LogicException('Param type : ' . $type . ' not found');
        }

        $paramService = 'armd_cms.usage_param.' . $type;
        if (!$this->container->has($paramService)) {
            throw new \LogicException('Service for param type : ' . $type . ' does not exist');
        }

        return $this->container->get($paramService);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param string $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function validateMainDef()
    {
        if (!is_array($this->usageTypeDef)) {
            throw new \InvalidArgumentException('usagetypes must be defined as array');
        }
        if (!count($this->usageTypeDef)) {
            throw new \InvalidArgumentException('usagetypes must be defined as array');
        }
        $arrayDef = array('types', 'group');
        foreach($this->usageTypeDef as $blockName => $oneDefBlock) {
            if (!in_array($blockName, array_merge($arrayDef, $this->arrayDefOptional))) {
                throw new \InvalidArgumentException(sprintf('Invalid block of usagetypes definition named "%s"', $oneDefBlock));
            }
        }

        foreach($arrayDef as $blockName) {
            if (!isset($this->usageTypeDef[$blockName])) {
                throw new \InvalidArgumentException(sprintf('Skipped block usagetypes definition named "%s"', $blockName));
            }
        }
    }

    /**
     * @param $typeName
     * @param $typeDef
     * @throws \InvalidArgumentException
     */
    public function validateUsageTypeDef($typeName, $typeDef) {
        $typeName = trim($typeName);
        if (!is_string($typeName) || !trim($typeName)) {
            throw new \InvalidArgumentException('Invalid UsageType name');
        }

        foreach($this->typeFieldsReq as $field) {
            if (!isset($typeDef[$field])) {
                throw new \InvalidArgumentException(sprintf('Definition of UsageType must have "%s" field', $field));
            }
        }
        $allPossibleFields = array_merge($this->typeFieldsReq, $this->typeFieldsOptional);

        foreach($typeDef as $field => $oneOption) {
            if (!in_array($field, $allPossibleFields)) {
                throw new \InvalidArgumentException(sprintf('Invalid block of UsageType definition named "%s"', $field));
            }
        }
        if (isset($typeDef['route'])) {
            $this->validateRoute($typeDef['route']);
        }
    }

    /**
     * @param string $groupName
     * @param array $groupDef
     * @throws \InvalidArgumentException
     */
    public function validateUsageGroupDef($groupName, array $groupDef) {
        $groupName = trim($groupName);
        if (!is_string($groupName) || !trim($groupName)) {
            throw new \InvalidArgumentException('Invalid Group UsageType name');
        }

        foreach($this->groupFieldsReq as $field) {
            if (!isset($groupDef[$field])) {
                throw new \InvalidArgumentException(sprintf('Definition of Group UsageType must have "%s" field', $field));
            }
        }
        foreach($groupDef as $field => $oneOption) {
            if (!in_array($field, array_merge($this->groupFieldsReq, $this->groupFieldsOptional))) {
                throw new \InvalidArgumentException(sprintf('Invalid block of Group UsageType definition named "%s"', $field));
            }
        }
        if (!is_array($groupDef['types'])) {
            throw new \InvalidArgumentException('"types" block of Group UsageType definition must be array');
        }
        foreach($groupDef['types'] as $oneType) {
            if (!is_string($oneType) || !trim($oneType) ) {
                throw new \InvalidArgumentException('"types" block of Group UsageType definition must be array of names UsageType');
            }
        }
    }

    /**
     * @param $routeDef
     * @throws \InvalidArgumentException
     */
    public function validateRoute(array $routeDef) {
        if (!isset($routeDef['pattern'])) {
            throw new \InvalidArgumentException('Miss pattern for route definition');
        }
        $arrayAllowFields = array('pattern', 'requirements', 'defaults');
        foreach(array_keys($routeDef) as $field) {
            if (!in_array($field, $arrayAllowFields)) {
                throw new \InvalidArgumentException(sprintf('Invalid field for definition route named "%s"', $field));
            }
        }
    }
}