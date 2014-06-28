<?php

namespace Armd\ContentAbstractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Index;

use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * Armd\ContentAbstractBundle\Entity\ContentLog
 *
 * @ORM\Table(
 *  name="content_log_entries",
 *  indexes={
 *      @index(name="log_class_lookup_idx", columns={"object_class"}),
 *      @index(name="log_date_lookup_idx", columns={"logged_at"}),
 *      @index(name="log_user_lookup_idx", columns={"username"})
 *  }
 * )
 * @ORM\Entity(repositoryClass="Gedmo\Loggable\Entity\Repository\LogEntryRepository")
 */
class ContentLog extends AbstractLogEntry
{
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}