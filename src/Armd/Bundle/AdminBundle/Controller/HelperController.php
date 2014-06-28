<?php

namespace Armd\Bundle\AdminBundle\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sonata\AdminBundle\Controller\HelperController as BaseController;

class HelperController extends BaseController
{

    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getShortObjectDescriptionAction(Request $request)
    {
        $code     = $request->get('code');
        $objectId = $request->get('objectId');
        $uniqid   = $request->get('uniqid');

        $admin = $this->pool->getInstance($code);

        if (!$admin) {
            throw new NotFoundHttpException();
        }

        $admin->setRequest($request);

        if ($uniqid) {
            $admin->setUniqid($uniqid);
        }

        if(!$objectId || !($object = $admin->getObject($objectId))) {
            return new Response();
        }

        $description = 'no description available';
        foreach (array('getAdminTitle', 'getTitle', 'getName', '__toString') as $method) {
            if (method_exists($object, $method)) {
                $description = call_user_func(array($object, $method));
                break;
            }
        }

        /* Fix gallery link to use proper context */
        $urlParams = array('id' => $objectId);
        if(method_exists($object, 'getContext')) {
            $urlParams['context'] = $object->getContext();
        }
        
        $url = $admin->generateUrl('edit', $urlParams);

        $htmlOutput = $this->twig->render($admin->getTemplate('short_object_description'),
            array(
                'description' => $description,
                'object' => $object,
                'url' => $url
            )
        );

        return new Response($htmlOutput);
    }
}
