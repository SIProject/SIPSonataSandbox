<?php

namespace SIP\ResourceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Armd\NewsBundle\Entity\BaseNews;
use Armd\ContentAbstractBundle\Model\ResultCacheableInterface;

/**
 * @ORM\Entity(repositoryClass="Armd\NewsBundle\Repository\NewsRepository")
 * @ORM\Table(name="content_news")
 */
class News extends BaseNews implements ResultCacheableInterface
{
    /**
     * @return array
     */
    public function getCacheKeys()
    {
        return array('id');
    }
}