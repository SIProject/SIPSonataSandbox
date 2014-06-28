<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */

namespace Armd\Bundle\CmsBundle\UsageType\Param;

use Armd\Bundle\CmsBundle\Manager\PageManager;
use Armd\Bundle\CmsBundle\Validator\Constraints\Template;

class TemplateParam extends BaseParam
{
    protected $controller;

    protected $action;

    /**
     * @var \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    protected $pageManager;

    /**
     * @param \Armd\Bundle\CmsBundle\Manager\PageManager $pagaManager
     */
    public function __construct(PageManager $pageManager)
    {
        $this->pageManager = $pageManager;
    }

    /**
     * @param $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param null $paramValue
     * @return array
     */
    public function getParamData($paramValue = null)
    {
        return array(
            $this->getName(),
            'genemu_jqueryselect2_choice',
            array(
                'data' => $this->getValue(),
                'choices'  => $this->getChoiceList(),
                'label' => $this->getTitle(),
                'required' => $this->isRequirements(),
                'configs' => array('allowClear' => $this->isRequirements()?false: true),
                'constraints' => array(
                    new Template()
                )
            )
        );
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageType $usageType
     * @return array
     */
    public function getChoiceList()
    {
        $choices = array();
        $themeService = $this->pageManager->getTemplateService();
        try {
            $moduleTemplates = $themeService->getModuleTemplates($this->getModuleName());
            foreach ($moduleTemplates->getTemplates() as $template) {
                $choices[$template->getName()] = "{$template->getTitle()} ({$template->getName()})";
            }
        } catch (\Exception $e ) {}
        return $choices;
    }
}
