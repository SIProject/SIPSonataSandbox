<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class BlocksController extends BaseResponseController
{
    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @var array
     */
    protected $sonataAdmin;

    /**
     * @abstract
     * @param \Armd\Bundle\CmsBundle\Entity\Page|\Armd\Bundle\CmsBundle\Entity\PageType $page
     * @return string
     */
    abstract public function getBlocksGridPath($page);

    /**
     * @abstract
     * @param \Armd\Bundle\CmsBundle\Entity\Page|\Armd\Bundle\CmsBundle\Entity\PageType $page
     */
    abstract public function getContainers($page);

    /**
     * @abstract
     * @return string
     */
    abstract public function getAssociationFieldName();

    /**
     * @param $view
     * @param array $parameters
     * @param null|Response $response
     * @return Response
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        if ( $parameters['action'] == 'edit' ) {
            $view = $this->admin->getBlocksTemplate();
            /**
             * @var \Symfony\Component\Form\FormView $form
             */
            $form = $parameters['form'];

            $parameters['blockPath']  = 'ArmdCmsBundle::blocks.html.twig';
            if ( $this->get('templating')->exists($blockPath = $this->getBlocksGridPath($form->vars['value'])) ) {
                $parameters['blockPath'] = $blockPath;
            }
            $parameters['containers']   = $this->getContainers($form->vars['value']);
            $parameters['sonata_admin'] = $this->getSonataAdmin();
            $parameters['id']           = "{$this->admin->getUniqid()}_{$this->getAssociationFieldName()}";
        }
        return parent::render($view, $parameters);
    }

    /**
     * @param $fieldName
     * @return array
     */
    public function getSonataAdmin()
    {
        if (!$this->sonataAdmin) {
            $this->sonataAdmin = $this->admin->getForm()->get($this->getAssociationFieldName())->getConfig()->getAttribute('sonata_admin');
        }

        return $this->sonataAdmin;
    }

    /**
     * @return \Sonata\DoctrineORMAdminBundle\Admin\FieldDescription
     */
    public function getFieldDescription()
    {
        $sonata_admin = $this->getSonataAdmin();

        if (!isset($sonata_admin['field_description'])) {
            throw new NotFoundHttpException("Can't find field description!");
        }

        return $sonata_admin['field_description'];
    }

    /**
     * @param string $usageServiceName
     * @return null|\Armd\Bundle\CmsBundle\UsageType\BaseUsageService
     */
    public function getUsageService($usageServiceName)
    {
        if ($this->container->has("{$usageServiceName}.usagetype")) {
            return $this->container->get("{$usageServiceName}.usagetype");
        }

        return null;
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    public function getPageManager()
    {
        return $this->container->get('armd_cms.page_manager');
    }

    /**
     * @return \Symfony\Component\Translation\TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->container->get('translator');
    }
}