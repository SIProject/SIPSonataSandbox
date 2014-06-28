<?php

namespace Armd\Bundle\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class CatchAllController extends BaseController
{
    public function indexAction()
    {
        return new Response();
    }
}
