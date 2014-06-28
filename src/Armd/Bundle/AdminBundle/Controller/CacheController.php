<?php

namespace Armd\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

use Symfony\Component\HttpFoundation\Response;

class CacheController extends Controller {
    private $env = null;

    private $debug = null;

    private $name = null;

    private $filesystem = null;

    private $cacheDir = null;

    private $pidFile = null;

    /**
     * @param string $env
     * @param boolean $debug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function buttonAction($env, $debug = null)
    {
        $this->env = $env;
        $this->debug = !empty($debug) ? true : false;

        return $this->render(
            'ArmdAdminBundle:Cache:button.html.twig',
            array(
                'env' => $this->env,
                'debug' => $this->debug,
                'busy' => $this->isBusy()
            )
        );
    }

    /**
     * @param string $env
     * @param boolean $debug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function statusAction($env, $debug = null)
    {
        $this->env = $env;
        $this->debug = !empty($debug) ? true : false;

        return $this->setResponse(array(
            'busy' => $this->isBusy()
        ));
    }

    /**
     * @param string $env
     * @param boolean $debug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function clearAction($env, $debug = null)
    {
        $this->env = $env;
        $this->debug = !empty($debug) ? true : false;
        $success = false;

        if (!$this->isBusy()) {
            $success = $this->executeClear();
        }

        return $this->setResponse(array(
            'busy' => $this->isBusy(),
            'success' => $success
        ));
    }

    /**
     * @return string
     */
    protected function getCacheDir()
    {
        if (!$this->cacheDir) {
            $this->cacheDir = $this->container->getParameter('kernel.root_dir') .'/cache/' .$this->env;
        }

        return $this->cacheDir;
    }

    /**
     * @return string
     */
    protected function getPidFile()
    {
        if (!$this->pidFile) {
            $this->pidFile = $this->container->getParameter('kernel.root_dir') .'/cache/clear-' .$this->env;
        }

        return $this->pidFile;
    }

    /**
     * @return \Symfony\Component\Filesystem\Filesystem
     */
    protected function getFilesystem()
    {
        if (!$this->filesystem) {
            $this->filesystem = $this->container->get('filesystem');
        }

        return $this->filesystem;
    }

    /**
     * @return boolean
     */
    protected function isBusy()
    {
        return $this->getFilesystem()->exists($this->getPidFile());
    }

    /**
     * @return boolean
     */
    protected function executeClear()
    {
        $cacheDir = $this->getCacheDir();
        $oldCacheDir  = $cacheDir .'_old';
        $warmupDir = $cacheDir .'_new';

        if (!is_writable($cacheDir)) {
            return false;
        }

        $this->getFilesystem()->touch($this->getPidFile());
        $this->container->get('cache_clearer')->clear($cacheDir);
        $this->executeWarmup($warmupDir);

        rename($cacheDir, $oldCacheDir);
        rename($warmupDir, $cacheDir);

        $this->getFilesystem()->remove($oldCacheDir);
        $this->getFilesystem()->remove($this->getPidFile());

        return true;
    }

    protected function executeWarmup($warmupDir)
    {
        $this->getFilesystem()->remove($warmupDir);

        $parent = $this->container->get('kernel');
        $class = get_class($parent);
        $namespace = '';

        if (false !== $pos = strrpos($class, '\\')) {
            $namespace = substr($class, 0, $pos);
            $class = substr($class, $pos + 1);
        }

        $kernel = $this->getTempKernel($parent, $namespace, $class, $warmupDir);
        $kernel->boot();

        $kernel->getContainer()->get('cache_warmer')->warmUp($warmupDir);

        // fix container files and classes
        $regex = '/'.preg_quote($this->getTempKernelSuffix(), '/').'/';
        
        $finder = new Finder();
        
        foreach ($finder->files()->name(get_class($kernel->getContainer()).'*')->in($warmupDir) as $file) {
            $content = file_get_contents($file);
            $content = preg_replace($regex, '', $content);

            // fix absolute paths to the cache directory
            $content = preg_replace('/'.preg_quote($warmupDir, '/').'/', preg_replace('/_new$/', '', $warmupDir), $content);

            file_put_contents(preg_replace($regex, '', $file), $content);
            unlink($file);
        }

        // fix meta references to the Kernel
        foreach ($finder->files()->name('*.meta')->in($warmupDir) as $file) {
            $content = preg_replace(
                '/C\:\d+\:"'.preg_quote($class.$this->getTempKernelSuffix(), '"/').'"/',
                sprintf('C:%s:"%s"', strlen($class), $class),
                file_get_contents($file)
            );
            file_put_contents($file, $content);
        }
    }

    /**
     * @return string
     */
    protected function getTempKernelSuffix()
    {
        if (null === $this->name) {
            $this->name = '__'.uniqid().'__';
        }

        return $this->name;
    }

    protected function getTempKernel(KernelInterface $parent, $namespace, $class, $warmupDir)
    {
        $suffix = $this->getTempKernelSuffix();
        $rootDir = $parent->getRootDir();
        $code = <<<EOF
<?php

namespace $namespace
{
    class $class$suffix extends $class
    {
        public function getCacheDir()
        {
            return '$warmupDir';
        }

        public function getRootDir()
        {
            return '$rootDir';
        }

        protected function getContainerClass()
        {
            return parent::getContainerClass().'$suffix';
        }
    }
}
EOF;
        $this->container->get('filesystem')->mkdir($warmupDir);
        file_put_contents($file = $warmupDir.'/kernel.tmp', $code);
        require_once $file;
        @unlink($file);
        $class = "$namespace\\$class$suffix";
        
        return new $class($this->env, $this->debug);
    }

    /**
     * @param array $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setResponse(array $data = null)
    {
        return new Response(
            json_encode(array(
                'data' => $data ? $data: null,
                'status' => 200,
                'error' => '',
            )),
            200,
            array('Content-Type' => 'application/json')
        );
    }
}