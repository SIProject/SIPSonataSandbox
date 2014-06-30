<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\TextBundle\Controller;

use Armd\ContentAbstractBundle\Controller\Controller;
use Armd\Bundle\CmsBundle\UsageType\UsageType;

use Doctrine\ORM\EntityManager;

class TextController extends Controller
{
    protected function getEntityId()
    {
        return $this->params->getParam('text_id')->getValue();
    }
    
    public function itemAction()
    {
        $entity = $this->getEntityRepository()->setId($this->getEntityId())->getQuery()->getOneOrNullResult();

        if (!$entity && $this->getRequest()->attributes->get("_is_main")) {
            throw $this->createNotFoundException("Unable to find entity");
        }
        
        return $this->renderCms(array('entity' => $entity));
    }
}