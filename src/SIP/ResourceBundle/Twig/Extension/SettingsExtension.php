<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Twig\Extension;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class SettingsExtension extends \Twig_Extension {

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    /**
     * {@inheritdoc}
     */
    public function getFunctions() {
        return array(
            'get_setting' => new \Twig_Function_Method($this, 'getSetting'),
            'declOfNum'   => new \Twig_Function_Method($this, 'declOfNum', array('is_safe' => array('html'))),
        );
    }

    /**
     * @param string $code
     * @return int
     */
    public function getSetting ($code) {
        return $this->container->get('sip_resource.helper.settings')->get($code);
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'settings';
    }

    function declOfNum($number, $titles)
    {
        $cases = array (2, 0, 1, 1, 1, 2);
        return $number." ".$titles[ ($number%100 > 4 && $number %100 < 20) ? 2 : $cases[min($number%10, 5)] ];
    }
}