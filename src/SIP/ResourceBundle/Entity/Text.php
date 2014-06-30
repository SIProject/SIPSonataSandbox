<?php

namespace SIP\ResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Armd\TextBundle\Entity\BaseText;

/**
 * @ORM\Entity(repositoryClass="Armd\ContentAbstractBundle\Repository\BaseContentRepository")
 * @ORM\Table(name="content_text")
 */
class Text extends BaseText
{
}