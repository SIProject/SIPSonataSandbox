<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Bundle\TwigBundle\TwigEngine;

use Armd\Bundle\CmsBundle\Services\TemplateService;
use Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer;

class TemplateValidator extends ConstraintValidator
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param mixed $value
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($templateName = $this->checkTemplate($value)) {
            $this->context->addViolation($constraint->message, array('%string%' => $templateName));
        }
    }

    /**
     * @param $templateName
     * @return bool|string
     */
    public function checkTemplate($templateName)
    {
        $themeService = $this->getPageManager()->getTemplateService();

        foreach ( $this->getUsageTypeContainer()->getTypes() as $usageType ) {
            $templatePath = $themeService->getTemplatePath(
                $usageType->getModuleName(),
                $usageType->getController(),
                $usageType->getAction(),
                $templateName);
            if ( !$this->getTemplating()->exists($templatePath . '.html.twig') ) {
                return $templatePath . '.html.twig';
            }
        }
        return false;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer
     * @throws \Symfony\Component\Validator\Exception\ValidatorException
     */
    public function getUsageTypeContainer()
    {
        if ($this->getRequest()->getSession()->has('usageServiceName') &&
            $this->getRequest()->getSession()->has('usageTypeName')) {
            if ($usageService = $this->getPageManager()
                                     ->getUsageService($this->getRequest()->getSession()->get('usageServiceName'))) {
                if ($usageTypeContainer = $usageService->getContainerType($this->getRequest()->getSession()->get('usageTypeName'))) {
                    return $usageTypeContainer;
                }

                throw new ValidatorException("Can not find UsageTypeContainer by name: {$this->getRequest()->getSession()->get('usageTypeName')}");
            }

            throw new ValidatorException("Can not find UsageService by name: {$this->getRequest()->getSession()->get('usageServiceName')}");
        }

        throw new ValidatorException("Don't have information of usageServiceName or usageTypeName");
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    public function getPageManager()
    {
        return $this->container->get('armd_cms.page_manager');
    }

    /**
     * @return \Symfony\Bundle\TwigBundle\TwigEngine
     */
    public function getTemplating()
    {
        return $this->container->get('templating');
    }
}