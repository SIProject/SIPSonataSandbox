<?php
/*
 * Store one possible usage type for module
 *
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\UsageType;

use Symfony\Component\Routing\Route;

class UsageType
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var \Symfony\Component\Routing\Route
     */
    protected $route = null;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam[]
     */
    protected $params = array();

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $moduleName;

    /**
     * @var string
     */
    protected $action;

    /**
     * @param string $moduleName
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $controller
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
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
     * @return \Armd\Bundle\CmsBundle\UsageType\Param\BaseParam[]
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * @param \Symfony\Component\Routing\Route $route
     */
    public function setRoute(Route $route)
    {
        $this->route = $route;
    }

    /**
     * @return \Symfony\Component\Routing\Route
     */
    public function getRoute()
    {
        return $this->route;
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
     * @return null|string
     */
    public function getTemplateValue()
    {
        return $this->getParam('template')->getValue();
    }
}
