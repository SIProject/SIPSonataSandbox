<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\AdminBundle\Block;

use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Form\FormMapper;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Sonata\AdminBundle\Validator\ErrorElement;

/**
 *
 * @author     Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class ActionBlockService extends BaseBlockService
{
    /**
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected $kernel;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param string                                            $name
     * @param \Symfony\Component\Templating\EngineInterface     $templating
     * @param \Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     */
    public function __construct($name, ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct($name, $this->container->get('templating'));
        $this->kernel = $this->container->get('http_kernel');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(BlockContextInterface $block, Response $response = null)
    {
        $parameters = (array)json_decode($block->getSetting('parameters'), true);
        $parameters = array_merge($parameters, array('_block' => $block));

        $settings = $block->getSettings();
        try {
            $parameters['_controller'] = $settings['action'];
            $subRequest = $this->container->get('request')->duplicate(array(), null, $parameters);

            $actionContent = $this->kernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST)->getContent();
        } catch (\Exception $e) {
            throw $e;
        }

        $content = self::mustache($block->getSetting('layout'), array(
            'CONTENT' => $actionContent
        ));

        return $this->renderResponse('ArmdAdminBundle:Block:block_core_action.html.twig', array(
            'content'   => $content,
            'block'     => $block->getBlock(),
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
    {
        // TODO: Implement validateBlock() method.
    }

    /**
     * {@inheritdoc}
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('layout', 'textarea', array()),
                array('action', 'text', array()),
                array('parameters', 'text', array()),
            )
        ));
    }

    /**
     * @static
     * @param $string
     * @param array $parameters
     * @return mixed
     */
    static public function mustache($string, array $parameters)
    {
        $replacer = function ($match) use ($parameters) {
            return isset($parameters[$match[1]]) ? $parameters[$match[1]] : $match[0];
        };

        return preg_replace_callback('/{{\s*(.+?)\s*}}/', $replacer, $string);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Action (core)';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'layout'      => '{{ CONTENT }}',
            'action'      => 'SonataBlockBundle:Block:empty',
            'parameters'  => '{}'
        ));
    }
}