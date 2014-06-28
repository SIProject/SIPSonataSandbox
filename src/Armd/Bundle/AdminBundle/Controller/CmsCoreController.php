<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Controller\CoreController;

use Armd\Bundle\AdminBundle\Entity\Favorites;

class CmsCoreController extends CoreController
{
    /**
     * @var array
     */
    protected $blocks;

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dashboardAction()
    {
        $this->setBlocks(array($this->initActionBlock("ArmdAdminBundle:Main:index")), true);
        return $this->renderBlock($this->blocks);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function logAction()
    {
        $this->setBlocks(array($this->initActionBlock("ArmdAdminBundle:Log:index")), true);
        return $this->renderBlock($this->blocks);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function statisticsAction()
    {
        $this->setBlocks(array($this->initActionBlock("ArmdAdminBundle:Statistic:index")), true);
        return $this->renderBlock($this->blocks);
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Controller\Response
     */
    public function dashboardGroupAction($group)
    {
        $this->setBlocks($this->container->getParameter('sonata.admin.configuration.dashboard_blocks'), true);
        $this->blocks[0]['settings']['groups'] = array($group);

        return $this->renderBlock($this->blocks);
    }

    /**
     * @param array $block
     */
    public function addBlock(array $block)
    {
        $this->blocks[] = $block;
    }

    /**
     * @param array $blocks
     * @param bool $addTip
     */
    public function setBlocks(array $blocks, $addTip = false)
    {
        foreach ( $blocks as $block ) {
            $this->addBlock($block);
        }

        if ( $addTip ) {
            $this->addBlock($this->initTextBlock($this->getRandTip()));
        }
    }

    /**
     * @param $content
     * @param string $position
     * @return array
     */
    public function initTextBlock($content, $position = 'right')
    {
        return array('type' => 'sonata.block.service.text',
                     'position' => $position,
                     'settings' => array('content' => $content));
    }

    /**
     * @param $content
     * @param string $position
     * @return array
     */
    public function initActionBlock($controller, $position = 'left')
    {
        return array('type' => 'sonata.block.service.action',
                     'position' => $position,
                     'settings' => array('action' => $controller,
                                         'layout' => '{{CONTENT}}'));
    }

    /**
     * @return string
     */
    public function getRandTip()
    {
        $tips = parse_ini_file(dirname(__DIR__) . '/Resources/public/tips/tips', true, INI_SCANNER_RAW);
        return $tips['tips'][array_rand($tips['tips'])];
    }

    /**
     * @param array $blocks
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderBlock(array $blocks = array())
    {
        return $this->render($this->container->get('sonata.admin.pool')->getTemplate('dashboard'), array(
            'base_template'   => $this->getBaseTemplate(),
            'admin_pool'      => $this->container->get('sonata.admin.pool'),
            'blocks'          => $blocks
        ));
    }

    /**
     * @param $serviceId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addFavoritAction($serviceId)
    {
        $favorite = new Favorites;
        $favorite->setServiceId($serviceId);

        $em = $this->getDoctrine()->getManager();
        $em->persist($favorite);
        $em->flush();

        return $this->setResponse();
    }

    /**
     * @param $serviceId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteFavoritAction($serviceId)
    {
        $favorite = $this->getDoctrine()->getRepository('ArmdAdminBundle:Favorites')->findOneBy(array('serviceId' => $serviceId));
        $em->remove($favorite);
        $em->flush();

        return $this->setResponse();
    }

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
}