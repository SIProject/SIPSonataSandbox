<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;

class ArmdCmsExtension extends Extension
{
    /**
     * @param array $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('admin.yml');
        $loader->load('services.yml');
        $loader->load('twig.yml');
        $loader->load('formTypes.yml');
        $loader->load('listeners.yml');
        $loader->load('type.yml');
        $loader->load('validators.yml');
        $loader->load('profiler.yml');

        $container->setParameter('armd_cms.ignore_route_patterns', $config['ignore_route_patterns']);
        $container->setParameter('armd_cms.data_collector.twig.display_in_wdt', $config['profiler']['twig']['display_in_wdt']);
        $container->setParameter('armd_cms.data_collector.twig.enabled', $config['profiler']['twig']['enabled']);

        $this->loadTemplatesDefinition($config, $container);
        $this->loadUsageTypeDefinition($config, $container);
    }

    /**
     * @param $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function loadTemplatesDefinition($config, ContainerBuilder $container)
    {
        $container->setParameter('armd_cms.templates.source_bundle', $config['templates']['source_bundle']);
        $container->setParameter('armd_cms.templates.layouts.definition', $config['templates']['layouts']);
        $container->setParameter('armd_cms.templates.modules.definition', $config['templates']['modules']);
    }

    /**
     * @param $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function loadUsageTypeDefinition($config, ContainerBuilder $container)
    {
        $params = array();
        foreach ($container->findTaggedServiceIds('usage_params') as $id => $attributes) {
            $paramServiceAray = explode('.', $id);
            $params[$paramServiceAray[count($paramServiceAray) -1]] = $id;
        }

        $container->setParameter('armd_cms.usage_param.params', $params);
        $container->setParameter('armd_cms.usagetype.class', $config['usagetype']['class']);
    }
}
