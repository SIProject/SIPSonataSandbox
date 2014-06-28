<?php

/**
 * Провайдер Google Analytics для получения статистики
 */
namespace Armd\Bundle\AdminBundle\Statistic\Provider;

use Armd\Bundle\AdminBundle\Statistic\Extra\Gapi;

class GoogleAnalyticsStatisticProvider
{
    /**
     * Google Analytics client
     *
     * @var \Armd\Bundle\AdminBundle\Statistic\Extra\Gapi
     */
    protected $ga;

    /**
     * Конструктор
     *
     * @param $appId string
     * @param $appSecret string
     * @param $clientLogin string
     * @param $clientPassword string
     */
    public function __construct($appId, $appSecret, $clientLogin='', $clientPassword='')
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->clientLogin = $clientLogin;
        $this->clientPassword = $clientPassword;

        $this->ga = new Gapi($clientLogin, $clientPassword);
    }

    public function getData($params = array())
    {
        $gaProfileId = $params['id'];

        $sort_metric = null;
        $filter      = null;
        $start_date  = $params['date1'];
        $end_date    = $params['date2'];
        $start_index = null;
        $max_results = null;

        $this->ga->requestReportData(
            $gaProfileId,
            array('date'),
            array('pageviews', 'visits', 'visitors'),
            $sort_metric,
            $filter,
            $start_date,
            $end_date,
            $start_index,
            $max_results
        );
        $res = $this->ga->getResults();

        $page_views = 0;
        $visitors = 0;
        $visits = 0;

        foreach ($res as $row) {
            $page_views += $row->getPageviews();
            $visitors += $row->getVisitors();
            $visits += $row->getVisits();
        }

        $data = array(
            'totals' => array(
                'page_views' => $page_views,
                'visitors' => $visitors,
                'visits' => $visits,
            ),
        );

        return $data;
    }

    /**
     * Получить статистику
     * 
     * @param $params array
     */
    public function getStatistic($params = array())
    {
        $statData = array();

        $groups = array(
            array(
                'name'  => 'day',
                'date1' => strtotime('-1 day'),
                'date2' => time(),
            ),
            array(
                'name'  => 'week',
                'date1' => strtotime('-1 week'),
                'date2' => time(),
            ),
            array(
                'name'  => 'month',
                'date1' => strtotime('-1 month'),
                'date2' => time(),
            ),
        );

        foreach ($groups as $group) {
            $date1 = $group['date1'];
            $date2 = $group['date2'];
            $data = $this->getData(array(
                'id' => $params['id'],
                'group' => $group['name'],
                'date1' => date('Y-m-d', $date1),
                'date2' => date('Y-m-d', $date2),
            ));
            $statData[$group['name']] = array(
                'dateStr'   => date('d.m.Y', $date1) .' - '. date('d.m.Y', $date2),
                'pageViews' => $data['totals']['page_views'],
                'visits'    => $data['totals']['visits'],
                'visitors'  => $data['totals']['visitors'],
            );
        }

        return $statData;
    }
    
    /**
     * Получить токен (заглушка)
     *
     * @param $params array
     * @param $grantType string
     */
    public function getToken($params = array(), $grantType = 'password')
    {
        return true;
    }

}
