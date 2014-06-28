<?php
/*
 * (c) Suhinin Ilja <isuhinin@armd.ru>
 */
namespace Armd\Bundle\CmsBundle\Profiler\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CmsDataCollector implements DataCollectorInterface
{
    protected $pageId;

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Symfony\Component\HttpFoundation\Response $response
     * @param \Exception $exception
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->pageId = $request->get('_page_id');
    }

    /**
     * @return mixed
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        $data = array(
            'pageId' => $this->pageId
        );

        return serialize($data);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'admin_link';
    }
}
