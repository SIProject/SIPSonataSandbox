<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */
namespace Armd\Bundle\CmsBundle\Entity;

interface ContainerIntrface {

    public function hasSetting($name);

    public function getSetting($name);

    public function getUsageService();

    public function getUsageType();

    public function getSettings();
}