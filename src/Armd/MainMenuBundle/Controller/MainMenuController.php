<?php

namespace Armd\MainMenuBundle\Controller;

use Armd\ContentAbstractBundle\Controller\Controller;
use Armd\Bundle\CmsBundle\Entity\Page;

class MainMenuController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function toplevelsAction()
    {
		return $this->renderCms(array('items' => $this->getTree()));
	}

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
	public function statusAction()
    {
		return $this->renderCms(array('items' => $this->getStatus()));
	}

    /**
     * Получить плоский список страниц по параметрам
     *
     * @return \Armd\Bundle\CmsBundle\Entity\Page[]
     */
	protected function getList(Page $startNode = null)
    {
		$em = $this->getEntityManager();
        $entityName = $this->getEntityName();
        $root = $this->getCurrentRoot();
		
		$query = $em
		    ->createQueryBuilder()
		    ->select('node')
		    ->from($entityName, 'node')
		    ->orderBy('node.root, node.lft', 'ASC')
		    ->where('node.root = :root')
			->andWhere('node.menuEnabled = true')
			->andWhere('node.lvl < ' . ($this->getDepth() + $this->getLevel()))
			->andWhere('node.lvl >= ' . $this->getLevel())
            ->andWhere("(select count(sn)
                         from {$entityName} as sn
                         where sn.lft<=node.lft and
                               sn.rgt>=node.rgt and
                               sn.root = :root and
                               sn.menuEnabled = false) = 0")
            ->setParameter('root', $root);

        if (!is_null($startNode)) {
            $query->andWhere('node.lft > :lft')
                  ->andWhere('node.rgt < :rgt')
                  ->setParameter('lft', $startNode->getLft())
                  ->setParameter('rgt', $startNode->getRgt());
        }
		
		$result = $query->getQuery()->getArrayResult();
		
		$path = array();
		foreach ($this->getStatus() as $item)
			$path[] = $item->getId();

		foreach ($result as $key => $value) {
            $result[$key]['url'] = $this->getRequest()->getBaseUrl() . $result[$key]['url'];
			$result[$key]['current'] = in_array($value['id'], $path) ? 1 : 0;
        }

		return $result;
	}		

	/**
	 * путь от текущей страницы до корня
	 */
    protected function getStatus()
    {
        return $this->getEntityRepository()->getPath($this->getCurrentPage());
	}
	 
	 /**
	  * построить дерево из плоского списка
	  */
    protected function getTree()
    {
        return $this->getEntityRepository()->buildTree($this->getList($this->getNode()));
    }

    /**
     * @return string
     */        
    public function getEntityName()
    {
        return "ArmdCmsBundle:Page";
    }
	
    /**
     * @return \Doctrine\ORM\EntityManager
     */
	protected function getEntityManager() {
	    return $this->getDoctrine()->getManager();
	}
	
	/**
	 * root текущей страницы
	 * 
	 * @return integer
	 */
	protected function getCurrentRoot()
    {
	    return $this->getCurrentPage()->getRoot();
	}
	 
    /**
     * @return \Armd\Bundle\CmsBundle\Entity\Page
     */
    protected function getCurrentPage()
    {
        return $this->getPageManager()->getCurrentPage();
    }

    /**
     * @return int
     */
    protected function getDepth()
    {
        return max (1, intval($this->getParams()->getParam('depth')->getValue()));
    }

    /**
     * @return null|\Armd\Bundle\CmsBundle\Entity\Page
     */
    protected function getNode()
    {
        if ($node = intval($this->getParams()->getParam('node')->getValue())) {
            return $this->getDoctrine()->getRepository($this->getEntityName())->find(intval($node));
        }
        return null;
    }

    /**
     * @return int
     */
    protected function getLevel()
    {
        return max (1, intval($this->getParams()->getParam('level')->getValue())) - 1;
    }
	
		
}
