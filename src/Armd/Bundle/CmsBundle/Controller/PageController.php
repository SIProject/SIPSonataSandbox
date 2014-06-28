<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Controller;

class PageController extends BlocksController
{
    /**
     * @param \Armd\Bundle\CmsBundle\Entity\Page $page
     * @return null|array
     */
    public function getContainers($page)
    {
        $parameterContainers = array();

        if ($pageContainers = $page->getPageContainers()) {
            /** @var \Armd\Bundle\CmsBundle\Entity\PageContainer $pageContainer */
            foreach ($pageContainers as $pageContainer) {

                $containerName = $pageContainer->getContainer()->getArea()->getName();

                $parameterContainers[$containerName] =
                    array('usageType'    => $this->getTranslator()->trans($pageContainer->getUsageType(), array(), "messages", null),
                          'usageService' => $this->getTranslator()->trans($pageContainer->getUsageService(), array(), "messages", null),
                          'url'          => $this->getFieldDescription()->getAssociationAdmin()->generateUrl('edit', array('id' => $pageContainer->getId())),
                          'deleteUrl'    => $this->getFieldDescription()->getAssociationAdmin()->generateUrl('delete', array('id' => $pageContainer->getId())),
                          'id'           => $pageContainer->getId(),
                          'adminListUrl' => '',
                          'isMain'       => $pageContainer->getContainer()->getIsMain());

                if ($pageContainer->getUsageService() &&
                    ($usageService = $this->getUsageService($pageContainer->getUsageService())) &&
                    $usageService->getAdminId() && $this->container->has($usageService->getAdminId())) {

                    $parameterContainers[$containerName]['adminListUrl'] =
                        $this->container->get($usageService->getAdminId())->generateUrl('list');
                }
            }
        }

        if ($containers = $page->getPageType()->getContainers()) {
            foreach ($containers as $container) {
                if (!isset($parameterContainers[$container->getArea()->getName()])) {

                    /** @var \Armd\Bundle\CmsBundle\Entity\Container $container */
                    $parameterContainers[$container->getArea()->getName()] =
                        array('usageType'    => $this->getTranslator()->trans($container->getUsageType(), array(), "messages", null),
                              'usageService' => $this->getTranslator()->trans($container->getUsageService(), array(), "messages", null),
                              'url'          => $this->getFieldDescription()->getAssociationAdmin()->generateUrl('createPageContainer', array('containerId' => $container->getId(), 'pageId' => $page->getId())),
                              'containerId'  => $container->getId(),
                              'adminListUrl' => '',
                              'isMain'       => $container->getIsMain()
                        );

                    if ($container->getUsageService() &&
                        ($usageService = $this->getUsageService($container->getUsageService())) &&
                        $usageService->getAdminId() && $this->container->has($usageService->getAdminId())) {

                        $parameterContainers[$container->getArea()->getName()]['adminListUrl'] =
                            $this->container->get($usageService->getAdminId())->generateUrl('list');
                    }
                }
            }
        }

        return empty($parameterContainers)? null: $parameterContainers;
    }

    /**
     * @param \Armd\Bundle\CmsBundle\Entity\Page $page
     * @return string
     */
    public function getBlocksGridPath($page)
    {
        return $this->getPageManager()->getTemplateService()->getLayout($page->getPageType()->getLayout())->getBlocksGridPath();
    }

    /**
     * @return string
     */
    public function getAssociationFieldName()
    {
        return 'pageContainers';
    }
}