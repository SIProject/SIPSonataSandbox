<?php

namespace Armd\Bundle\CmsBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Armd\Bundle\CmsBundle\Entity\Page;

use Sonata\AdminBundle\Exception\ModelManagerException;
use Doctrine\ORM\EntityManager;

class BaseTreeController extends BaseResponseController
{
    /**
     * return the Response object associated to the tree action
     *
     * @return Response
     */
    public function listAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $request = $this->get('Request');
        $params = array( 'sites'     => $this->getSites(),
            'action'    => 'tree',
            'node_path' => '',
            'node_id'   => 0,
            'menulist'  => null);

        if ( $selectSite = $request->query->get('select_site') ) {
            $params['siteId'] = (int)$request->query->get('select_site');
        }
        return $this->render($this->admin->getTreeTemplate(), $params);
    }

    /**
     * @return array
     */
    public function getSites()
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('ArmdCmsBundle:Site')
                    ->findAll();
    }

    /**
     * return the Response object associated to the sublist action
     *
     * @return Response
     */
    public function sublistAction()
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $request = $this->get('Request');
        $parentId = (int) $request->query->get('node');
        if ( 0 == $parentId ) { $parentId = null; }

        $em = $this->getDoctrine()->getManager();
        $items = $em->getRepository('ArmdCmsBundle:Page')
                    ->findBy(array('parent'=>$parentId, 'site' => (int)$request->query->get('select_site')),
                             array('lft'=>'ASC'));

        $pageAdmin = $this->container->get('armd_cms.admin.page');

        $data = array();
        foreach($items as $item) {
            $data[] = array(
                'id'         => $item->getId(),
                'text'       => $item->getTitle(),
                'expandable' => (bool) count($item->getChildren()),
                'expanded'   => ($item->getLvl()==0), // раскрываем первый уровень вложенности
                'pageUrl'    => $item->getUrl(),
                'qtip'       => $item->getId(),
                'lft'        => $item->getLft(),
                'addUrl'     => $pageAdmin->generateObjectUrl('createchild', $item),
                'editUrl'    => $pageAdmin->generateObjectUrl('edit', $item),
                'copyUrl'    => $pageAdmin->generateObjectUrl('copy', $item),
                'deleteUrl'  => $pageAdmin->generateObjectUrl('delete', $item),
				'prnt'       => (bool) $item->getParent(),
			);
        }

        return $this->setResponse($data);
    }

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @param $id
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        if($this->isXmlHttpRequest() || false) {
            $t = $this->get('translator');
            $id = $this->get('request')->get($this->admin->getIdParameter());
            $object = $this->admin->getObject($id);

            if (!$object) {
                return $this->setExeptionResponse(404, sprintf('unable to find the object with id : %s', $id));
            }

            if (false === $this->admin->isGranted('DELETE', $object)) {
                return $this->setExeptionResponse(403, 'access denied');
            }

            try {
                $this->admin->delete($object);
            } catch ( ModelManagerException $e ) {
                return $this->setExeptionResponse(500, $t->trans('flash_delete_error'));
            }
            return $this->setResponse();
        } else {
            return parent::deleteAction($id);

        }
    }

    /**
     * return the Response object associated to the create action
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function copyAction($id = null)
    {
        return $this->createObjectByExists($id, 'copy');
    }

    /**
     * return the Response object associated to the create action
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createChildAction($id = null)
    {
        return $this->createObjectByExists($id, 'createchild');
    }

    /**
     * @return object a new object instance
     */
    private function getObjectByExist($existObj, $action)
    {
        if('copy' == $action) {
            $object = clone $existObj;
        } elseif('createchild' == $action) {
            $object = $this->admin->getNewInstance();
            $object->setParent($existObj);
        } else {
            return $existObj;
        }

        return $object;
    }

    /**
     * return the Response object associated to the copy or createChild actions
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createObjectByExists($id, $action)
    {
        if (false === $this->admin->isGranted('CREATE')) {
            throw new AccessDeniedException();
        }

        $exists_object = $this->admin->getObject($id);

        if (!$exists_object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $object = $this->getObjectByExist($exists_object, $action);

        $this->admin->setSubject($object);

        $form = $this->admin->getForm();
        $form->setData($object);

        if ($this->get('request')->getMethod() == 'POST') {
            $form->bind($this->get('request'));

            $isFormValid = $form->isValid();

            if ($isFormValid && (!$this->isInPreviewMode() || $this->isPreviewApproved())) {
                if ($object instanceof \Armd\Bundle\CmsBundle\Model\CloneInterface) {
                    $object->cloneAssociation();
                }

                $this->admin->create($object);

                if ($this->isXmlHttpRequest()) {
                    return $this->renderJson(array(
                        'result' => 'ok',
                        'objectId' => $this->admin->getNormalizedIdentifier($object)
                    ));
                }

                $this->get('session')->getFlashBag()->add('sonata_flash_success','flash_create_success');
                return $this->redirectTo($object);
            }

            if (!$isFormValid) {
                if (!$this->isXmlHttpRequest()) {
                    $this->get('session')->getFlashBag()->add('sonata_flash_error', 'flash_create_error');
                }
            }
        }

        $view = $form->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($view, $this->admin->getFormTheme());

        return $this->render($this->admin->getTemplate('edit'), array(
            'action' => $action,
            'id'     => $id,
            'form'   => $view,
            'object' => $object,
        ));
    }

    /**
     * return the Response object associated to the create action
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function movePageNodeAction()
    {
        if (false === $this->admin->isGranted('CREATE') && false === $this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $request = $this->get('Request');
        $t = $this->get('translator');

        $sourceNodeId = (int) $request->query->get('nodeId');
        $targetNodeId = (int) $request->query->get('targetNodeId');
        $dropPosition = $request->query->get('dropPosition');

        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var PageRepository $repo
         */
        $repo = $em->getRepository('ArmdCmsBundle:Page');
        $sourceNode = $repo->find($sourceNodeId);
        $targetNode = $repo->find($targetNodeId);

        try {
            switch ($dropPosition) {
                case 'before':
                    // Ищем узел, предшествующий текущему, в пределах этого уровня.
                    $prevSiblingNodes = $repo->getPrevSiblings($targetNode);
                    if (false == $prevSiblingNodes) {
                        // У текущего узла нет предыдущего собрата.
                        // Вставляем как первый дочерний узел
                        $repo->persistAsFirstChildOf($sourceNode, $targetNode->getParent());
                    } else {
                        // Нашли узел, предшествующий текущему, в пределах этого уровня.
                        $prevSiblingNode = array_pop($prevSiblingNodes);
                        // Вставляем перетаскиваемый узел за ним.
                        $repo->persistAsNextSiblingOf($sourceNode, $prevSiblingNode);
                    }
                    break;

                case 'after':
                    $repo->persistAsNextSiblingOf($sourceNode, $targetNode);
                    break;

                case 'append':
                    // Сделать $targetNode парентом $sourceNode
                    $repo->persistAsLastChildOf($sourceNode, $targetNode);
                    break;
            }
            $em->flush();

        } catch ( \Exception $e ) {
            return $this->setExeptionResponse(500, $e->getMessage());
        }
        $data = array(
            'nodeId' => $sourceNodeId,
            'targetNodeId' => $targetNodeId,
            'dropPosition' => $dropPosition,
        );

        return $this->setResponse($data);
    }


    /**
     * return the Response object associated to the create action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function moveMenuNodeAction()
    {
        if (false === $this->admin->isGranted('CREATE') && false === $this->admin->isGranted('DELETE')) {
            throw new AccessDeniedException();
        }

        $request = $this->get('Request');
        $t = $this->get('translator');

        $sourceNodeId = (int) $request->query->get('drag');
        $targetNodeId = (int) $request->query->get('drop');
        $dropPosition = $request->query->get('position');

        /**
         * @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var PageRepository $repo
         */
        $repo = $em->getRepository('ArmdMenuBundle:MenuElement');
        $sourceNode = $repo->find($sourceNodeId);
        $targetNode = $repo->find($targetNodeId);

        try {
            switch ($dropPosition) {
                case 'before':
                    // Ищем узел, предшествующий текущему, в пределах этого уровня.
                    $prevSiblingNodes = $repo->getPrevSiblings($targetNode);
                    if (false == $prevSiblingNodes) {
                        // У текущего узла нет предыдущего собрата.
                        // Вставляем как первый дочерний узел
                        $repo->persistAsFirstChildOf($sourceNode, $targetNode->getParent());
                    } else {
                        // Нашли узел, предшествующий текущему, в пределах этого уровня.
                        $prevSiblingNode = array_pop($prevSiblingNodes);
                        // Вставляем перетаскиваемый узел за ним.
                        $repo->persistAsNextSiblingOf($sourceNode, $prevSiblingNode);
                    }
                    break;

                case 'after':
                    $repo->persistAsNextSiblingOf($sourceNode, $targetNode);
                    break;

                case 'append':
                    // Сделать $targetNode парентом $sourceNode
                    $repo->persistAsLastChildOf($sourceNode, $targetNode);
                    break;
            }
            $em->flush();

        } catch ( \Exception $e ) {
            $this->setExeptionResponse(500, $e->getMessage());
            return $this->setExeptionResponse(500, $e->getMessage());
        }
        $data = array(
            'nodeId' => $sourceNodeId,
            'targetNodeId' => $targetNodeId,
            'dropPosition' => $dropPosition,
        );


        return $this->setResponse($data);
    }
}