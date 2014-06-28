<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Model;

class Template
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
     * @var string
     */
    protected $moduleName;

    /**
     * @var string
     */
    protected $sourceBundleName;

    /**
     * @param $name
     * @param $sourceBundleName
     * @param array $templateDefinition
     */
    public function __construct($name, $sourceBundleName, $moduleName, array $templateDefinition)
    {
        $this->setName($name);
        $this->setSourceBundleName($sourceBundleName);
        $this->setModuleName($moduleName);

        if ($templateDefinition['title']) {
            $this->setTitle($templateDefinition['title']);
        }
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
     * @param string $sourceBundleName
     */
    public function setSourceBundleName($sourceBundleName)
    {
        $this->sourceBundleName = $sourceBundleName;
    }

    /**
     * @return string
     */
    public function getSourceBundleName()
    {
        return $this->sourceBundleName;
    }

    /**
     * @param string $controllerName
     * @param string $actionName
     * @return string
     */
    public function getTemplatePath($controllerName, $actionName)
    {
        $controllerName = lcfirst($controllerName);
        $actionName     = lcfirst($actionName);
        return "$this->sourceBundleName:module/$this->moduleName/$this->name:$actionName.$controllerName";
    }
}
