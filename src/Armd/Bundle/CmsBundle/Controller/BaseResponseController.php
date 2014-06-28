<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Armd\Bundle\AdminBundle\Entity\Favorites;

use Sonata\AdminBundle\Controller\CRUDController;

class BaseResponseController extends CRUDController
{
    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setResponse(array $data = null)
    {
        return new Response(
            json_encode(array(
                'data' => $data? $data: null,
                'status' => 200,
                'error' => '',
            )),
            200,
            array('Content-Type' => 'application/json')
        );
    }

    /**
     * @param $errorMessage
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setExeptionResponse($errorMessage = 'access denied', $errorCode = 403)
    {
        return new Response(
            json_encode(array(
                'status' => $errorCode,
                'error' => $errorMessage,
            )),
            $errorCode,
            array('Content-Type' => 'application/json'));
    }


    /**
     * @param null $id
     * @param $revision
     */
    public function historyRevertRevisionAction($id = null, $revision = null)
    {
        if (false === $this->admin->isGranted('EDIT')) {
            throw new AccessDeniedException();
        }

        $id = $this->get('request')->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $manager = $this->getAuditManager();

        if (!$manager->hasReader($this->admin->getClass())) {
            throw new NotFoundHttpException(sprintf('unable to find the audit reader for class : %s', $this->admin->getClass()));
        }

        /**
         * @var \Armd\Bundle\AuditBundle\Model\AuditReader $reader
         */
        $reader = $manager->getReader($this->admin->getClass());

        // retrieve the revisioned object
        $rev_object = $reader->find($this->admin->getClass(), $id, $revision);

        if (!$rev_object) {
            throw new NotFoundHttpException(sprintf('unable to find the targeted object `%s` from the revision `%s` with classname : `%s`', $id, $revision, $this->admin->getClass()));
        }

        $em = $this->getDoctrine()->getManager();
        $this->admin->getFormBuilder();

        foreach($this->admin->getFormFieldDescriptions() as $f => $v) {
            $getter = 'get'.ucfirst($f);
            $setter = 'set'.ucfirst($f);
            if(method_exists($object, $getter) && method_exists($object, $setter)) {
                $object->$setter($rev_object->$getter());
            }
        }

        $em->persist($object);
        $em->flush();

        return $this->redirect($this->admin->generateObjectUrl('history', $object));
    }

    /**
     * @return \Sonata\AdminBundle\Model\AuditManager
     */
    public function getAuditManager()
    {
        return $this->get('sonata.admin.audit.manager');
    }
}