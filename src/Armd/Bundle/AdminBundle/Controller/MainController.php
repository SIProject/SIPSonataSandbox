<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\AdminBundle\Controller;

use Armd\Bundle\AdminBundle\Entity\Favorites;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * @var \Sonata\AdminBundle\Admin\Admin
     */
    protected $admin;

    public function indexAction()
    {
        $favoritesElems = null;
        if ($favorites = $this->getFavorites()) {
            foreach ($favorites as $favorite) {
                if (!isset($favoritesElems[$favorite->getServiceId()])) {
                    try {
                        $this->admin = $this->get($favorite->getServiceId());
                        if ($this->admin->isGranted('CREATE') || $this->admin->isGranted('LIST')) {
                            $favoritesElems[$favorite->getServiceId()] = array('label'  => $this->admin->getLabel(),
                                                  'create'    => $this->admin->generateUrl('create'),
                                                  'list'      => $this->admin->generateUrl('list'),
                                                  'serviceId' => $favorite->getServiceId());
                        }
                    } catch ( \Exception $e ) {}
                }
            }
        }

        return $this->render("ArmdAdminBundle:Block:block_admin_main.html.twig", array('favorites' => $favoritesElems));
    }

    /**
     * @return \Armd\Bundle\AdminBundle\Entity\Favorites[]
     */
    public function getFavorites()
    {
        return $this->getDoctrine()->getRepository('ArmdAdminBundle:Favorites')->findAll();
    }
}