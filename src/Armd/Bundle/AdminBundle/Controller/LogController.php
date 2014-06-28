<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LogController extends Controller
{
    public function indexAction()
    {
        return $this->render("ArmdAdminBundle:Block:block_admin_log.html.twig");
    }
}