<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\MainMenuBundle\Model;

interface TreeMenuInterface
{
    /**
     * Get menuEnabled
     *
     * @return string
     */
    public function getMenuEnabled();

    /**
     * Set menuEnabled
     *
     * @param integer $menuEnabled
     */
    public function setMenuEnabled($menuEnabled);

    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl();

    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt();
}