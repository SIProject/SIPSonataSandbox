<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RepositoryController extends Controller
{
    public function indexAction()
    {
        $ps = $this->container->get('armd_admin.platform_service');
        $modules = $ps->getModuleList();
        $avalableModules = $ps->getAvalableModuleList();

        $bundles = array();

        foreach($avalableModules as $module) {
            $ver = false;
            $verId = false;
            foreach($module->versions as $versionData) {
                if(!$ver) {
                    $ver = $versionData->number;
                    $verId = $versionData->id;
                }
                if($versionData->number > $ver) {
                    $ver = $versionData->number;
                    $verId = $versionData->id;
                }
            }
            $bundles[] = array(
                'name' => $module->moduleName,
                'version' => $ver,
                'versionId' => $verId,
                'info' => $module->description,
                'id' => $module->id,
                'inactive' => true
            );
        }

        foreach($modules as $module) {
            $description = $ps->getModuleDescription($module->module_id);

            $bundles[] = array(
                'name' => $module->module_name,
                'version' => $module->number,
                'info' => $description,
                'id' => $module->module_id,
                'inactive' => false
            );
        }

        return $this->render("ArmdAdminBundle:Block:block_admin_repository.html.twig", array('bundles' => $bundles));
    }

    public function moduleAction()
    {
        $module = $this->container->get('Request')->query->get('module');

        $ps = $this->container->get('armd_admin.platform_service');
        $delModules = $ps->getPlatformParam('del_modules');
        $delModules = explode(',', (string)$delModules);

        $vData = $ps->getVersionList( $module );

        if(in_array($vData->id, $delModules)) {
            $canDelete = true;
        } else {
            $canDelete = false;
        }
        $bundle = array(
            'moduleName' => $vData->moduleName,
            'description' => $vData->description,
            'can_update' => false,
            'can_delete' => $canDelete,
            'moduleId' => $vData->id,
        );

        $versions = array();
        $ver = false;
        foreach($vData->versionList as $v) {
            $versions[] = array(
                'number' => $v->number,
                'date' => strtotime($v->issueDate),
            );

            if(!$ver) {
                $ver = $v->number;
            }
            if($v->number > $ver) {
                $ver = $v->number;
            }
        }

        $bundle['lastversion'] = $ver;

        return $this->render("ArmdAdminBundle:Block:block_admin_repository_module.html.twig", array('bundle' => $bundle, 'versions' => $versions));
    }
}
