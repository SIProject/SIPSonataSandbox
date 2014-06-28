<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PageContainerController extends ContainerSettingsController
{
    public function createPageContainerAction($containerId, $pageId)
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        /**
         * @var \Armd\Bundle\CmsBundle\Entity\Container $container
         */
        $container = $this->container->get('armd_cms.admin.container')->getObject($containerId);
        /**
         * @var \Armd\Bundle\CmsBundle\Entity\Page $page
         */
        $page = $this->container->get('armd_cms.admin.page')->getObject($pageId);
        /**
         * @var \Armd\Bundle\CmsBundle\Entity\PageContainer $object
         */
        $object = $this->admin->getNewInstance();

        $object->setContainer($container);
        $object->copySettings($container->getSettings());
        $object->setUsageService($container->getUsageService());
        $object->setUsageType($container->getUsageType());

        $object->setPage($page);

        $this->admin->setSubject($object);

        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bindRequest($this->get('request'));

            if ($form->isValid()) {
                $this->admin->create($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result' => 'ok',
                        'objectId' => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                $this->get('session')->setFlash('sonata_flash_success','flash_create_success');
                // redirect to edit mode
                return $this->redirectTo($object);
            }
            $this->get('session')->setFlash('sonata_flash_error', 'flash_create_error');
        }

        $view = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate('edit'), array(
            'action'         => 'create',
            'form'           => $view,
            'selectUsageUrl' => $this->container->get('armd_cms.admin.container')->generateUrl('edit', array('id' => $containerId)),
            'object'         => $object,
        ));
    }
}