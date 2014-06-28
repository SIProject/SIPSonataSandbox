<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\ContentAbstractBundle\Model;

interface EntityInterface
{
    /**
     * @abstract
     * @return int
     */
    public function getId();

    /**
     * @abstract
     * @return string
     */
    public function __toString();
}