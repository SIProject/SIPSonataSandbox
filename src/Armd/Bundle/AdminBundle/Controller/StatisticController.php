<?php

/*
 * Id приложения: 4805469f751e4863ba246649b5888769
 * Пароль приложения: 9b73cac970d74cba9b289447919490a8
 */

namespace Armd\Bundle\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StatisticController extends Controller
{
    public function indexAction()
    {
        $allStatData = array();

        // Провайдеры статистики: Yandex-метрика, Google Analytics
        $statistics = $this
            ->getDoctrine()
            ->getRepository('ArmdAdminBundle:Statistic')
            ->findBy(array('isActive' => true));

        foreach ($statistics as $stat) {
            $providerClass = 'Armd\Bundle\AdminBundle\Statistic\Provider\\' .$stat->getProviderClass() .'StatisticProvider';

            if (class_exists($providerClass)) {
                $provider = new $providerClass(
                    $stat->getAppId(),
                    $stat->getAppSecret(),
                    $stat->getUserLogin(),
                    $stat->getUserPassword()
                );

                $accessToken = $provider->getToken();

                $statData = $provider->getStatistic(array('id' => $stat->getCounterId()));

                $allStatData[] = $this->renderView('ArmdAdminBundle:Statistic:Report/' .$stat->getProviderClass() .'.html.twig', array(
                    'statistic' => $stat,
                    'data' => $statData,
                ));
            }
        }

        return $this->render(
            'ArmdAdminBundle:Block:block_admin_statistic.html.twig',
            array('data' => $allStatData)
        );
    }

    public function yandexTokensAction()
    {
        print 'yandexTokensAction';
        exit;
    }

}