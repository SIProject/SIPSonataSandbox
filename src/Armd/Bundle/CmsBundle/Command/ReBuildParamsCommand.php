<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Armd\Bundle\CmsBundle\UsageType\BaseUsageService;

class ReBuildParamsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('armd:reBuildParams')
            ->setDescription('Комманда пересобирает параметры блоков')
            ->setHelp(<<<EOT
    Комманда пересобирает параметры блоков
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var \Armd\Bundle\CmsBundle\Entity\Container[] $containers
         * @var \Armd\Bundle\CmsBundle\Entity\Container[] $pageContainers
         */
        $containers = $this->getDoctrine()->getRepository('ArmdCmsBundle:Container')->findAll();
        $pageContainers = $this->getDoctrine()->getRepository('ArmdCmsBundle:PageContainer')->findAll();

        $count = 0;
        foreach ($containers as $container) {
            $container->setSettings($this->reBuildSettings($container->getSettings(), $count));
            $this->getDoctrine()->getManager()->persist($container);
        }
        $output->writeln("Обработано {$count} контейнеров типов страниц!");
        $count = 0;
        foreach ($pageContainers as $pageContainer) {
            $pageContainer->setSettings($this->reBuildSettings($pageContainer->getSettings(), $count));
            $this->getDoctrine()->getManager()->persist($pageContainer);
        }
        $output->writeln("Обработано {$count} контейнеров страниц!");

        $this->getDoctrine()->getManager()->flush();
    }

    /**
     * @param array $settings
     * @return array
     */
    public function reBuildSettings(array $settings = null, &$count)
    {
        $reBuildSettings = array();
        if ($settings) {
            foreach ($settings as $usageServiceName => $setting) {
                if ($this->getPageManager()->hasUsageService($usageServiceName) || 'undefined' == $usageServiceName) {
                    foreach ($setting as $usageServiceParamName => $usageServiceParam) {
                        if (strpos($usageServiceParamName, ':')) {
                            foreach ($usageServiceParam as $usageContainerParamName => $usageContainerParam) {
                                $reBuildSettings[$usageContainerParamName] = $usageContainerParam;
                            }
                        } elseif ($this->hasUsageType($this->getPageManager()->getUsageService($usageServiceName),
                                                      $usageServiceParamName)) {
                            foreach ($usageServiceParam as $usageTypeParamName => $usageTypeParam) {
                                $reBuildSettings[$usageTypeParamName] = $usageTypeParam;
                            }
                        } else {
                            $reBuildSettings[$usageServiceParamName] = $usageServiceParam;
                        }
                    }
                }
            }
        }

        if (empty($reBuildSettings)) {
            return $settings;
        }

        $count++;
        return $reBuildSettings;
    }

    /**
     * @param \Armd\Bundle\CmsBundle\UsageType\BaseUsageService $usageService
     * @param $usageTypeName
     * @return bool
     */
    public function hasUsageType(BaseUsageService $usageService, $usageTypeName)
    {
        foreach ($usageService->getContainerTypes() as $usageTypeContainer) {
            foreach ($usageTypeContainer->getTypes() as $usageType) {
                if ($usageType->getName() == $usageTypeName) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    public function getDoctrine()
    {
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @return \Armd\Bundle\CmsBundle\Manager\PageManager
     */
    public function getPageManager()
    {
        return $this->getContainer()->get('armd_cms.page_manager');
    }
}