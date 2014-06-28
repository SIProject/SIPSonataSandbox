<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sonata\AdminBundle\Exception\ModelManagerException;

use Armd\Bundle\CmsBundle\UsageType\BaseUsageService;
use Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer;
use Armd\Bundle\CmsBundle\UsageType\UsageType;

class ContainerSettingsController extends BaseResponseController
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    protected $pagaManager;

    /**
     * @var \Symfony\Component\Translation\TranslatorInterface
     */
    protected $translator;

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usageTypeAddAction()
    {
        return $this->usageTypeAction();
    }

    /**
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usageTypeAction($id = null)
    {
        if (false === $this->admin->isGranted('EDIT')) {
            $this->setExeptionResponse();
        }
        $data = array();

        $usageServiceName = $this->getRequest()->query->get('usageServiceName');

        if ( $usageServiceName ) {
            $this->pagaManager = $this->getPageManager();
            $this->translator = $this->getTranslator();

            if ( $usageService = $this->pagaManager->getUsageService($usageServiceName) ) {

                foreach ( $usageService->getContainerTypes() as $usageTypeName => $usageType ) {

                    $usageTypeTransName = 'usagetype.title.' . $usageServiceName . '.' . $usageTypeName;

                    $data['choise_list'][$usageTypeName] = $this->translator->trans($usageTypeTransName, array(), "messages", null);
                }
            }

            if ( $id ) {
                $object = $this->admin->getObject($id);
                $data['choised'] = $object->getUsageType();
            }
        }

        return $this->setResponse($data);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usageTypeParamsAddAction()
    {
        return $this->usageTypeParamsAction();
    }

    /**
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function usageTypeParamsAction($id = null)
    {
        if (false === $this->admin->isGranted('EDIT')) {
            $this->setExeptionResponse();
        }
        $data = array();

        $usageServiceName = $this->getRequest()->query->get('usageServiceName');
        $usageTypeName    = $this->getRequest()->query->get('usageTypeName');

        $this->getRequest()->getSession()->set('usageServiceName', $usageServiceName);
        $this->getRequest()->getSession()->set('usageTypeName', $usageTypeName);

        if ( $usageServiceName && $usageTypeName ) {
            $this->pagaManager = $this->getPageManager();

            if ( $usageService = $this->pagaManager->getUsageService($usageServiceName) ) {
                $usageTypes = $usageService->getContainerType($usageTypeName);

                $settings = array();
                if ( $id ) {
                    $object = $this->admin->getObject($id);
                    $settings = $object->getSettings();
                }

                $data = $this->getParams($usageTypes, $usageService, $settings);
            }
        }

        return $this->setResponse($data);
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\UsageTypeContainer $usageTypes
     * @param $settings
     * @return array
     */
    public function getParams(UsageTypeContainer $usageTypes, BaseUsageService $usageService, $settings)
    {
        $this->setEntityManager();

        $data = array();
        // Глобальные параметры UsegeService
        if ($usageService->getParams()) {
            foreach ($usageService->getParams() as $usageServiceParam) {
                $data[] = $usageServiceParam->setValueFromSetting($settings)->getParamData();
            }
        }

        // Параметры UsegeTypeContainer
        if ($usageTypes->getParams()) {
            foreach ($usageTypes->getParams() as $usageTypesParam) {
                $data[] = $usageTypesParam->setValueFromSetting($settings)->getParamData();
            }
        }
        // Параметры UsageType
        foreach ($usageTypes->getTypes() as $usageType) {
            foreach ($usageType->getParams() as $param) {
                $data[] = $param->setValueFromSetting($settings)->getParamData();
            }
        }

        $this->admin->setUniqid($this->getRequest()->query->get('uniqid'));
        $builder = $this->admin->getFormBuilder();
        $builder->add('settings', 'usageParamsType', array('required' => true, 'compound' => true, 'keys' => $data));
        $view = $builder->getForm()->createView();
        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return array('content' => $this->getTemplating()
                                       ->render('ArmdCmsBundle:Admin:form_field.html.twig',
                                                array('view' => $view)));
    }

    /**
     * @param mixed $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function deleteAction($id)
    {
        $id     = $this->get('request')->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('DELETE', $object)) {
            throw new AccessDeniedException();
        }

        if ($this->getRequest()->getMethod() == 'DELETE') {
            try {
                $this->admin->delete($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result'    => 'ok',
                        'objectId'  => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                $this->get('session')->setFlash('sonata_flash_success', 'flash_delete_success');
            } catch (ModelManagerException $e) {
                $this->get('session')->setFlash('sonata_flash_error', 'flash_delete_error');
            }

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render($this->admin->getTemplate('delete'), array(
            'object' => $object,
            'action' => 'delete'
        ));
    }

    /**
     * set em
     */
    public function setEntityManager()
    {
        $this->em = $this->getDoctrine()->getManager();
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

    /**
     * @return \Symfony\Bundle\TwigBundle\Debug\TimedTwigEngine
     */
    public function getTemplating()
    {
        return $this->container->get('templating');
    }
}