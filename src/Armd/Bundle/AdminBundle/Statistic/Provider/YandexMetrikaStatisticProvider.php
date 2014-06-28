<?php

/**
 * Провайдер Yandex.Metrika для получения статистики
 */
namespace Armd\Bundle\AdminBundle\Statistic\Provider;

class YandexMetrikaStatisticProvider extends BaseStatisticProvider
{
    /**
     * URL авторизации
     * 
     * @var string
     */
    protected $authUrl = 'https://oauth.yandex.ru/authorize';
    
    /**
     * URL получения токена
     * 
     * @var string
     */
    protected $tokenUrl = 'https://oauth.yandex.ru/token';
    
    /**
     * URL получения данных
     * 
     * @var string
     */
    protected $dataUrl = 'http://api-metrika.yandex.ru';
    
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
            $res = $this->getData('/stat/traffic/summary.json', array(
                'id' => $params['id'],
                'group' => $group['name'],
                'date1' => date('Ymd', $date1),
                'date2' => date('Ymd', $date2),
            ));
            $statData[$group['name']] = array(
                'dateStr'   => date('d.m.Y', $date1) .' - '. date('d.m.Y', $date2),
                'pageViews' => $res['totals']['page_views'],
                'visits'    => $res['totals']['visits'],
                'visitors'  => $res['totals']['visitors'],
            );
        }

        return $statData;
    }

}
