<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 *
 */
namespace Armd\Bundle\CmsBundle\UsageType;
use LogicException;

class UsageTypeContainer
{
    /**
     * @var \Armd\Bundle\CmsBundle\UsageType\UsageType[]
     */
    protected $items = array();

    /**
     * System name of container
     * @var string
     */
    protected $name;

    /**
     * @var \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam[]
     */
    protected $params = array();


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
     * @return \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam[]
     */
    public function getParams()
    {
        return $this->params;
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
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageType $type
     */
    public function addType(UsageType $type) {
        $this->items[$type->getName()] = $type;
    }

    /**
     * @param $name
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageType
     * @throws \LogicException
     */
    public function getType($name) {
        if (!isset($this->items[$name])) {
            throw new LogicException('Usage type named: ' . $name . ' not found');
        }
        return $this->items[$name];
    }

    /**
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageType[]
     */
    public function getTypes() {
        return $this->items;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return title based on UsageType names
     *
     * @return string
     */
    public function getTitle() {
        $title = array();
        foreach($this->items as $item) {
            $title[] = $item->getTitle();
        }
        $titleString = join('+', $title);
        return $titleString ? $titleString : $this->getName();
    }

}