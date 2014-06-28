<?php
/*
 * (c) Stepanov Andrey <isteep@gmail.com>
 */

namespace Armd\Bundle\CmsBundle\Tests\Service;

use PHPUnit_Framework_TestCase;

use Armd\Bundle\CmsBundle\Services\TemplateService;
use Armd\Bundle\CmsBundle\Model\Theme;

class TemplateServiceTest extends PHPUnit_Framework_TestCase
{
    public function testCreatePathFromArray()
    {
        $arrayDefinition = array(
            'bundle'    => 'TemplateBundle',
            'layout'    => 'Main',
            'module'    => 'News',
            'controller'=> 'Arhive',
            'action'    => 'list',
            'tempalate' => 'widget',
        );

        $service = new TemplateService();
        $path = $service->createPathFromArray($arrayDefinition);
        $expectPath = 'TemplateBundle:layout/Main/News/Arhive:list/widget';
        $this->assertEquals($expectPath,  $path, "Пути не совпадают");
    }

    public function testCreatePathFromArrayBaseModule()
    {
        $arrayDefinition = array(
            'bundle'    => 'TemplateBundle',
            'layout'    => null,
            'module'    => 'News',
            'controller'=> 'Arhive',
            'action'    => 'list',
            'tempalate' => 'Widget',
        );

        $service = new TemplateService();
        $path = $service->createPathFromArray($arrayDefinition);
        $expectPath = 'TemplateBundle:module/News/Widget:list.arhive';
        $this->assertEquals($expectPath,  $path, "Пути не совпадают");
    }

    public function testCreatePathFromModuleTemplate()
    {
        $arrayDefinition = array(
            'bundle'    => 'NewsBundle',
            'layout'    => null,
            'module'    => null,
            'controller'=> 'Arhive',
            'action'    => 'list',
            'tempalate' => 'widget',
        );

        $service = new TemplateService();
        $path = $service->createPathFromArray($arrayDefinition);
        $expectPath = 'NewsBundle:Arhive:list';
        $this->assertEquals($expectPath,  $path, "Пути не совпадают");
    }

    public function testGetLayoutPath()
    {
        $theme = new Theme\Theme();
        $layout = new Theme\Layout();
        $layout->setName('some');
        $theme->setName('ArmdCmsBundle');
        $theme->addLayout($layout);

        $service = new TemplateService();
        $service->setTheme($theme);
        $path = $service->getLayoutPath('some');
        $this->assertEquals("ArmdCmsBundle:layout/some:index", $path, "Путь к layout не верен");
    }



}
