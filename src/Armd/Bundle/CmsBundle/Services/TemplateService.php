<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Services;

use Armd\Bundle\CmsBundle\Model\Layout;
use Armd\Bundle\CmsBundle\Model\ModuleTemplate;

use Symfony\Component\DependencyInjection\ContainerInterface;

class TemplateService
{
    /**
     * @var string
     */
    protected $sourceBundleName;

    /**
     * @var array
     */
    protected $layoutsDefinition;

    /**
     * @var array
     */
    protected $modulesTemplateDefinition;

    /**
     * @var Armd\Bundle\CmsBundle\Model\ModuleTemplate[]
     */
    protected $modulesTemplate;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param $sourceBundleName
     * @param $layoutsDefinition
     * @param $modulesDefinition
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct($sourceBundleName, $layoutsDefinition, $modulesDefinition, ContainerInterface $container)
    {
        $this->setSourceBundleName($sourceBundleName);
        $this->buildLayoutsDefinition($layoutsDefinition);
        $this->buildModuleTemplatesDefinition($modulesDefinition);
        $this->container = $container;
    }

    /**
     * @param array $layoutsDefinition
     */
    public function buildLayoutsDefinition(array $layoutsDefinition)
    {
        foreach ($layoutsDefinition as $layoutName => $layoutDefinition) {
            $this->layoutsDefinition[$layoutName] = $layoutDefinition;
        }
    }

    /**
     * @param $name
     * @param array $layoutDefinition
     * @return \Armd\Bundle\CmsBundle\Model\Layout
     */
    public function buildLayout($name, array $layoutDefinition)
    {
        return new Layout($name, $this->sourceBundleName, $layoutDefinition);
    }

    /**
     * @param array $modulesDefinition
     */
    public function buildModuleTemplatesDefinition(array $modulesDefinition)
    {
        foreach ($modulesDefinition as $moduleName => $moduleDefinition) {
            $this->modulesTemplateDefinition[$moduleName] = $moduleDefinition;
        }
    }

    /**
     * @param string $sourceBundleName
     */
    public function setSourceBundleName($sourceBundleName)
    {
        $this->sourceBundleName = $sourceBundleName;
    }

    /**
     * @param $moduleName
     * @return bool
     */
    public function hasModuleTemplates($moduleName)
    {
        return isset($this->modulesTemplateDefinition[$moduleName]);
    }

    /**
     * @param $moduleName
     * @return null
     */
    public function getModulesTemplateDefinition($moduleName)
    {
        if ($this->hasModuleTemplates($moduleName)) {
            return $this->modulesTemplateDefinition[$moduleName];
        }

        return array();
    }

    /**
     * @param $moduleName
     * @return \Armd\Bundle\CmsBundle\Model\ModuleTemplate
     */
    public function getModuleTemplates($moduleName)
    {
        if (!isset($this->modulesTemplate[$moduleName])) {
            $this->modulesTemplate[$moduleName] = new ModuleTemplate($moduleName, $this->sourceBundleName, $this->getModulesTemplateDefinition($moduleName));
        }
        return $this->modulesTemplate[$moduleName];
    }

    /**
     * @param $moduleName
     * @param $controllerName
     * @param $actionName
     * @param $templateName
     * @return string
     */
    public function getTemplatePath($moduleName, $controllerName, $actionName, $templateName)
    {
        if ($this->hasModuleTemplates($moduleName)) {
            $moduleTemplates = $this->getModuleTemplates($moduleName);
            if ($moduleTemplates->hasTemplate($templateName)) {
                return $moduleTemplates->getTemplate($templateName)->getTemplatePath($controllerName, $actionName);
            }
        }

        return $this->container->get("$moduleName.usagetype")->getBundleName() . ":$controllerName:$actionName";
    }

    /**
     * @param $layoutName
     * @return \Armd\Bundle\CmsBundle\Model\Layout
     */
    public function getLayout($layoutName)
    {
        if (isset($this->layoutsDefinition[$layoutName])) {
            return $this->buildLayout($layoutName, $this->layoutsDefinition[$layoutName]);
        }

        return $this->getDefaultLayout();
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Model\Layout[]
     */
    public function getLayouts()
    {
        $layouts = array();

        foreach ($this->layoutsDefinition as $layoutName => $layoutDefinition) {
            $layouts[$layoutName] = $this->buildLayout($layoutName, $this->layoutsDefinition[$layoutName]);
        }

        return $layouts;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Model\Layout
     * @throws \LogicException
     */
    public function getDefaultLayout()
    {
        foreach ($this->layoutsDefinition as $key => $layoutDefinition) {
            if ($layoutDefinition['default']) {
                return $this->buildLayout($key, $layoutDefinition);
            }
        }

        throw new \LogicException("Cannot find default layout! Assign the default layout!");
    }
}
