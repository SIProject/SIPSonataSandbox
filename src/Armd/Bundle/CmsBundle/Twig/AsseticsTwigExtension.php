<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Twig;

use Twig_Extension;
use Twig_Function_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AsseticsTwigExtension extends \Twig_Extension
{
    protected $ressources = array();

    protected $environment;

    /**
     * @var array
     */
    protected $titles = array();

    /**
     * @var array
     */
    protected $javascrit = array();

    /**
     * @var array
     */
    protected $stylesheets = array();

    /**
     * @return array
     */
    public function getFunctions() {
        return array(
            'add_title'       => new Twig_Function_Method($this, 'add_title'),
            'add_javascrit'   => new Twig_Function_Method($this, 'add_javascrit'),
            'add_stylesheets' => new Twig_Function_Method($this, 'add_stylesheets'),

            'get_title'       => new Twig_Function_Method($this, 'get_title'),
            'get_javascrit'   => new Twig_Function_Method($this, 'get_javascrit'),
            'get_stylesheets' => new Twig_Function_Method($this, 'get_stylesheets'),
        );
    }

    /**
     * @param string | array $title
     */
    public function add_title($title)
    {
        if (is_array($title)) {
            foreach ($title as $t) {
                array_push($this->titles, $t);
            }

        } else {
            array_push($this->titles, $title);
        }
    }

    /**
     * @param string $media
     * @return string
     */
    public function add_javascrit($media, $order = 0)
    {
        if (array_search($media, $this->javascrit) === false) {
            $this->javascrit[] = array('path' => $media, 'order' => $order);
        }
    }

    /**
     * @param string $media
     * @return string
     */
    public function add_stylesheets($media, $order = 0)
    {
        if (array_search($media, $this->stylesheets) === false) {
            $this->stylesheets[] = array('path' => $media, 'order' => $order);
        }
    }

    /**
     * @return string
     */
    public function get_title($delim = ' ')
    {
        return $this->titles ? join($delim, $this->titles) : '';
    }

    /**
     * @param bool $reverse
     * @return string
     */
    public function get_javascrit($reverse = false)
    {
        uasort($this->javascrit, array($this, 'mediasCompare'));
        return $this->render('ArmdCmsBundle:Assets:javascript_assets.html.twig',
            array('medias' => $reverse? array_reverse($this->javascrit): $this->javascrit));
    }

    /**
     * @param bool $reverse
     * @return string
     */
    public function get_stylesheets($reverse = false)
    {
        uasort($this->stylesheets, array($this, 'mediasCompare'));
        return $this->render('ArmdCmsBundle:Assets:stylesheets_assets.html.twig',
            array('medias' => $reverse? array_reverse($this->stylesheets): $this->stylesheets));
    }

    /**
     * @param $x
     * @param $y
     * @return 0|1|-1
     */
    public function mediasCompare($x, $y)
    {
        if ($x['order'] == $y['order']) {
            return 0;
        } elseif($x['order'] < $y['order']) {
            return 1;
        } else {
            return -1;
        }
    }

    /**
     * @param string $template
     * @param array  $parameters
     *
     * @return mixed
     */
    public function render($template, array $parameters = array())
    {
        if (!isset($this->ressources[$template])) {
            $this->ressources[$template] = $this->environment->loadTemplate($template);
        }

        return $this->ressources[$template]->render($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'armd_cms_assetic_twig_extension';
    }
}
