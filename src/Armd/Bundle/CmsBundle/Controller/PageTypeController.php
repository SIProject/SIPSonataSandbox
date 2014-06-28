<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PageTypeController extends BlocksController
{
    /**
     * @param \Armd\Bundle\CmsBundle\Entity\PageType $containers
     * @return array|null
     */
    public function getContainers($pageType)
    {
        if ($containers = $pageType->getContainers()) {
            $parameterContainers = array();
            foreach ( $containers as $container ) {
                /** @var \Armd\Bundle\CmsBundle\Entity\Container $container */
                $parameterContainers[$container->getArea()->getName()] =
                    array('usageType'    => $this->getTranslator()->trans($container->getUsageType(), array(), "messages", null),
                          'usageService' => $this->getTranslator()->trans($container->getUsageService(), array(), "messages", null),
                          'url'          => $this->getFieldDescription()->getAssociationAdmin()->generateUrl('edit', array('id' => $container->getId())),
                          'deleteUrl'    => $this->getFieldDescription()->getAssociationAdmin()->generateUrl('delete', array('id' => $container->getId())),
                          'id'           => $container->getId(),
                          'adminListUrl' => '',
                          'isMain'       => $container->getIsMain());

                if ($container->getUsageService() &&
                    ($usageService = $this->getUsageService($container->getUsageService())) &&
                    $usageService->getAdminId() && $this->container->has($usageService->getAdminId())) {
                    $parameterContainers[$container->getArea()->getName()]['adminListUrl'] =
                        $this->container->get($usageService->getAdminId())->generateUrl('list');
                }
            }
            return $parameterContainers;
        }
        return null;
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function copyAction($id)
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $exists_object = $this->admin->getObject($id);

        if (!$exists_object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $object = clone $exists_object;

        $this->admin->setSubject($object);

        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            $isFormValid = $form->isValid();

            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                $this->admin->create($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result' => 'ok',
                        'objectId' => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                $this->get('session')->setFlash('sonata_flash_success','flash_create_success');
                return $this->redirectTo($object);
            }

            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->get('session')->setFlash('sonata_flash_error', 'flash_create_error');
                }
            }
        }

        $view = $form->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate('edit'), array(
            'action' => 'copy',
            'id'     => $id,
            'form'   => $view,
            'object' => $object,
        ));
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\PageType $pageType
     * @return string
     */
    public function getBlocksGridPath($pageType)
    {
        return $this->getPageManager()->getTemplateService()->getLayout($pageType->getLayout())->getBlocksGridPath();
    }

    /**
     * @return string
     */
    public function getAssociationFieldName()
    {
        return 'containers';
    }
}