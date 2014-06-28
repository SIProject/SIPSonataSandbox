<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\ContentAbstractBundle\Model;

interface ResultCacheableInterface
{
    /**
     * @abstract
     * @return array
     */
    public function getCacheKeys();
}