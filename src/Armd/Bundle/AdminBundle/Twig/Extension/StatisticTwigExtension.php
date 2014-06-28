<?php

namespace Armd\Bundle\AdminBundle\Twig\Extension;

use Twig_Extension;
use Twig_Function_Method;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StatisticTwigExtension extends Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getFunctions() {
        return array(
            'get_statistic_counters'  => new Twig_Function_Method($this, 'getStatisticCounters'),
        );
    }

    public function getName()
    {
        return 'statistic_extension';
    }
    
    /**
     * Получить счетчики.
     * 
     * @return string HTML счетчиков
     */
    public function getStatisticCounters()
    {
        $html = array();
        
        $query = $this->getEntityManager()
                      ->getRepository('Armd\Bundle\AdminBundle\Entity\Statistic')
                      ->createQueryBuilder('s')
                      ->where('s.isActive = :isActive')
                      ->setParameter('isActive', true)->getQuery();

        $statistics = $query->useResultCache(true, null, '_statistic_list')->execute();

        foreach ($statistics as $stat) {
            $html[] = $this->container->get('templating')->render(
                'ArmdAdminBundle:Statistic:Counter/' .$stat->getProviderClass() .'.html.twig',
                array('statistic' => $stat)
            );
        }
        
        return implode('', $html);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        return $this->container->get('doctrine.orm.entity_manager');
    }
}