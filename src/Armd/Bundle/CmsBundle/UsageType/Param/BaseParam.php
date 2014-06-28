<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\UsageType\Param;

abstract class BaseParam
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var mixed
     */
    protected $default;

    /**
     * @var mixed
     */
    protected $value = null;

    /**
     * @var bool
     */
    protected $requirements;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @var array
     */
    protected $allowFieldsDefault = array('title', 'default', 'type', 'requirements');

    /**
     * @var array
     */
    protected $allowFields = array();

    /**
     * @param string $name
     * @return \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $value
     */
    public function setDefault($value)
    {
        $this->default = $value;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param array $settings
     * @return BaseParam
     */
    public function setValueFromSetting(array $settings = null)
    {
        $paramValue = $this->getValue();
        if (isset($settings[$this->getName()])) {
            $paramValue = $settings[$this->getName()];
        }

        $this->value = $paramValue;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (is_null($this->value)) {
            return $this->getDefault();
        }
        return $this->value;
    }

    /**
     * @param bool $isRequirement
     */
    public function setRequirements($isRequirement = true)
    {
        $this->requirements = $isRequirement;
    }

    /**
     * @return bool
     */
    public function isRequirements()
    {
        return (bool)$this->requirements;
    }

    /**
     * @param $moduleName
     * @return BaseParam
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Fill param object from array values
     * @param array $defensions
     * @return \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam
     * @throws \InvalidArgumentException
     */
    public function fromArray(array $defensions)
    {
        foreach($defensions as $nameParam => $valueParam) {
            if ($nameParam == 'type') {
                continue;
            }
            $setMethod = 'set' . ucfirst($nameParam);
            if (!method_exists($this, $setMethod) ) {
                throw new \InvalidArgumentException(
                    sprintf("Class param do not have '%s' method for set '%s' attribute", $nameParam, $setMethod)
                );
            }

            $this->$nameParam = $valueParam;
        }

        return $this;
    }

    /**
     * @internal param $param
     * @return string
     */
    public function getType()
    {
        $fullClasName = get_class($this);
        $namespaceArray = explode("\\", $fullClasName);
        $typeNameEndPos = strpos($namespaceArray[count($namespaceArray) -1], 'Param');

        return strtolower( substr($namespaceArray[count($namespaceArray) -1], 0, $typeNameEndPos) );
    }

    /**
     * @internal param null $paramValue
     * @return array
     */
    public function getParamData()
    {
        return array($this->getName(), null, array('data' => $this->getValue(),
                                                   'label' => $this->getTitle(),
                                                   'required' => $this->isRequirements()));
    }

    /**
     * @param array $paramDef
     * @throws \InvalidArgumentException
     * @internal param array $paramsDef
     */
    public function validateParamDef(array $paramDef)
    {
        $arrayAllowFields = array_merge($this->allowFieldsDefault, $this->allowFields);

        foreach($paramDef as $param => $value) {
            if (!in_array($param, $arrayAllowFields)) {
                throw new \InvalidArgumentException(sprintf('Invalid field named "%s" for definition param', $param));
            }
        }
    }
}