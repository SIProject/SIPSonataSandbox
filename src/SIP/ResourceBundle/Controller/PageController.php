<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageController extends Controller
{
    /**
     * @Route("/{slug}", name="sip_staic_pages")
     * @Template()
     * @return array
     */
    public function indexAction($slug)
    {
        $page = $this->getDoctrine()
            ->getRepository('SIP\ResourceBundle\Entity\Page')
            ->findOneBySlug($slug);

        if($page)
            return array(
                'page' => $page
            );

        throw $this->createNotFoundException();
    }
}