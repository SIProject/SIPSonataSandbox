<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class MainController extends Controller
{
    /**
     * @Route("/", name="sip_resource_main")
     * @Template()
     * @return array
     */
    public function indexAction()
    {
        return array();
    }
}