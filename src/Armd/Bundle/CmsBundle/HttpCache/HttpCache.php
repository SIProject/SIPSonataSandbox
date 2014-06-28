<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\HttpCache;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HttpCache
{
    /**
     * @var string
     */
    protected $domain;

    /**
     * @var Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var int
     */
    protected $port;

    /**
     * Constructor
     *
     * @param string $domain the domain we want to purge urls from. only domain and port are used, path is ignored
     * @param array $ips space separated list of varnish ips to talk to
     * @param int $port the port the varnishes listen on (its the same port for all instances)
     */
    public function __construct(ContainerInterface $container, $port = 80)
    {
        $this->container = $container;
        $this->domain = $this->container->get('request')->getHost();
        $this->port = $port;
    }

    /**
     * Purge this absolute path at all registered cache server
     *
     * @param string $path Must be an absolute path
     * @throws \RuntimeException if connection to one of the varnish servers fails.
     */
    public function invalidatePath($path)
    {
        $request = "PURGE $path HTTP/1.0\r\n";
        $request.= "Host: {$this->domain}\r\n";
        $request.= "Connection: Close\r\n\r\n";

        $this->sendRequest($request);
    }

    /**
     * Force this absolute path to be refreshed
     *
     * @param string $path Must be an absolute path
     * @throws \RuntimeException if connection to one of the varnish servers fails.
     */
    public function refreshPath($path)
    {
        $request = "GET $path HTTP/1.0\r\n";
        $request.= "Host: {$this->domain}\r\n";
        $request.= "Cache-Control: no-cache, no-store, max-age=0, must-revalidate";
        $request.= "Connection: Close\r\n\r\n";

        $this->sendRequest($request);
    }

    /**
     * @param $request
     * @throws \RuntimeException
     */
    protected function sendRequest($request)
    {
        $fp = fsockopen($this->domain, $this->port, $errno, $errstr, 2);
        if (!$fp) {
            throw new \RuntimeException("$errstr ($errno)");
        }

        fwrite($fp, $request);

        // read answer to the end, to be sure varnish is finished before continuing
        while (!feof($fp)) {
            fgets($fp, 128);
        }

        fclose($fp);
    }
}