<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class UsageParamsType extends AbstractType
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
     * @param \Symfony\Component\Form\FormView $view
     * @param \Symfony\Component\Form\FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $class = isset($view->vars['attr']['class'])? $view->vars['attr']['class'] . ' ': '';
        $typeClass = $this->checkSession()? 'selectUsageParams-quiet': 'selectUsageParams';

        $view->vars['attr']['class'] = $class . $typeClass;
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $options = array('compound' => false);

        if ($this->checkSession()) {
            $usageService = $this->getUsageService($this->getRequest()->getSession()->get('usageServiceName'));
            $usageTypeContainer = $usageService->getContainerType($this->getRequest()->getSession()->get('usageTypeName'));

            $params = array_merge($usageService->getParams(), $usageTypeContainer->getParams());
            foreach ($usageTypeContainer->getTypes() as $usageType) {
                $params = array_merge($params, $usageType->getParams());
            }
            $keys = array();
            foreach ($params as $param) {
                array_push($keys, $param->getParamData());
            }

            $options = array('compound' => true, 'keys' => $keys);
        }

        $resolver->setDefaults($options);
    }

    /**
     * Shortcut to return the request service.
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->container->get('request');
    }

    /**
     * @param $usageServiceName
     * @return \Armd\Bundle\CmsBundle\UsageType\BaseUsageService
     */
    public function getUsageService($usageServiceName)
    {
        if ($this->container->has($usageServiceName . '.usagetype')) {
            return $this->container->get($usageServiceName . '.usagetype');
        }

        return null;
    }

    /**
     * @return bool
     */
    public function checkSession()
    {
        return $this->getRequest()->getSession()->has('usageServiceName') &&
               $this->getRequest()->getSession()->get('usageServiceName') &&
               $this->getRequest()->getSession()->has('usageTypeName') &&
               $this->getRequest()->getSession()->get('usageTypeName') &&
               $this->getRequest()->getMethod() == 'POST';
    }

    public function getParent()
    {
        return 'sonata_type_immutable_array';
    }

    public function getName()
    {
        return 'usageParamsType';
    }
}