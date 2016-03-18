<?php
/**
 * Created by PhpStorm.
 * User: Felix
 * Date: 22.10.2015
 * Time: 14:53
 */

namespace SIP\ResourceBundle\Twig\Extension;

use Symfony\Bridge\Doctrine\RegistryInterface;

class MetaExtension extends \Twig_Extension
{
    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_meta_data', array($this, 'getMetaData'))
        );
    }

    public function getMetaData($key = null) {

        if ($key) {
            $metaData =  $this->doctrine->getRepository('SIP\ResourceBundle\Entity\MetaData')->findOneBy(array('metaKey' => $key));

            return ($metaData) ? $metaData : null;
        }

        return false;
    }

    public function getName()
    {
        return 'meta_extension';
    }
}