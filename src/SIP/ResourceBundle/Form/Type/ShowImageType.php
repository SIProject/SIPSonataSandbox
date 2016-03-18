<?php
/*
 * (c) Suhinin Ilja <iljasuhinin@gmail.com>
 */
namespace SIP\ResourceBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class ShowImageType extends AbstractType
{
    public function getParent()
    {
        return 'entity';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'show_sonata_image';
    }
}
