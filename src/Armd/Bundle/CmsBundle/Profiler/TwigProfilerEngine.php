<?php

namespace Armd\Bundle\CmsBundle\Profiler;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Armd\Bundle\CmsBundle\Profiler\DataCollector\TwigDataCollector;

/**
 * Profiling proxy to TwigEngine
 * References https://github.com/Elao/WebProfilerExtraBundle/blob/master/DataCollector/TwigDataCollector.php
 */
class TwigProfilerEngine extends TwigEngine
{
    protected $environment;
    protected $twigEngine;
    protected $collector;

    public function __construct(\Twig_Environment $environment, TwigEngine $twigEngine, TwigDataCollector $collector)
    {
        $this->environment = $environment;
        $this->twigEngine = $twigEngine;
        $this->collector = $collector;
    }

    /**
     * {@inheritdoc}
     */
    public function render($name, array $parameters = array())
    {
        $templatePath = null;

        $loader = $this->environment->getLoader();
        if ($loader instanceof \Twig_Loader_Filesystem) {
            $templatePath = $loader->getCacheKey($name);
        }
        $this->collector->collectTemplateData($name, $parameters, $templatePath);

        return $this->twigEngine->render($name, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function stream($name, array $parameters = array())
    {
        $this->twigEngine->stream($name, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        return $this->twigEngine->exists($name);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($name)
    {
        return $this->twigEngine->supports($name);
    }
}