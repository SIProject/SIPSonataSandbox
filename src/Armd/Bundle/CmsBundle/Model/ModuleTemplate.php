<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Model;

class ModuleTemplate
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Template[]
     */
    protected $templates = array();

    /**
     * @var string
     */
    protected $sourceBundleName;

    public function __construct($name, $sourceBundleName, array $moduleDefinition)
    {
        $this->setName($name);
        $this->setSourceBundleName($sourceBundleName);

        if (isset($moduleDefinition['templates'])) {
            foreach ($moduleDefinition['templates'] as $templateName => $templateDefinition) {
                $this->addTemplate($templateName, $templateDefinition);
            }
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
     * @param $name
     * @return bool
     */
    public function hasTemplate($name)
    {
        return isset($this->templates[$name]);
    }

    /**
     * @param $name
     * @param $definition
     */
    public function addTemplate($name, $definition)
    {
        $this->templates[$name] = new Template($name, $this->sourceBundleName, $this->name, $definition);
    }

    /**
     * @return Template[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @param $name
     * @return Template|bool
     */
    public function getTemplate($name)
    {
        if ($this->hasTemplate($name)) {
            return $this->templates[$name];
        }

        return false;
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
}
