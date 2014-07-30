<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-08-11 11:05 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Widget_MostLikedStoresController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	  if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')
		    || !Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')
	      ) {
		  $this->setNoRender($this);
		  return;
	  }
	  
    $this->view->widget = 'most_liked';
    $this->view->item_type = $item_type = 'page';
    $this->view->showTitle = false;
    if ($this->getElement()->getTitle() == null) {
      $this->view->showTitle = true;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('like.store_count', 9);
    $this->view->period = $period = $settings->getSetting('like.store_period', 1);

		$data = $this->getMostLiked($item_type, $ipp);
	  
		if (!$data) {
			$this->setNoRender();
			return ;
		}

    if ($period) {
      $week_paginator = $this->getMostLiked($item_type, $ipp, 'week');
      $this->view->week_likes = $week_paginator['paginator'];
      $this->view->week_counts = $week_paginator['counts'];

      $month_paginator = $this->getMostLiked($item_type, $ipp, 'month');
      $this->view->month_likes = $month_paginator['paginator'];
      $this->view->month_counts = $month_paginator['counts'];
    }

		$this->view->all_likes = $all_likes = $data['paginator'];
		if (!$all_likes->getTotalItemCount() && $all_likes) {
			$this->setNoRender();
			return ;
		}

		$this->view->all_likes->setItemCountPerPage($ipp);
		$this->view->all_counts = $data['counts'];

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $this->getElement()->setAttrib('class', 'like_widget_theme_' . $this->view->activeTheme());
  }

	protected function getMostLiked($type, $limit = null, $period = 'all')
	{
		if (!$type) {
			return false;
		}

		$api = Engine_Api::_()->getApi('page', 'store');
		$pagesTbl = Engine_Api::_()->getDbTable('pages', 'page');
		$table = Engine_Api::_()->getDbTable('likes', 'core');
		$name = $table->info('name');
		$pagesTblName = $pagesTbl->info('name');

		$select = $table->select()
			->setIntegrityCheck(false)
			->from(array('like' => $name), array('resource_id', 'like_count' => 'COUNT(*)'))
			->joinLeft($pagesTblName, $pagesTblName.'.page_id = like.resource_id', array())
			->order('like_count DESC')
			->group('like.resource_id');

		if ($limit) {
			$select->limit($limit);
		}

    if ($period == 'month') {
      $select->where('`like`.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
    } elseif ($period == 'week') {
      $select->where('`like`.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
    }
		$select = $api->setStoreIntegrity($select);
		$select
		->where('like.resource_type = ?', $type);
		$rawData = $table->getAdapter()->fetchPairs($select);
		$ids = array_keys($rawData);

		if (!empty($ids)) {
			return array(
				'paginator' => Zend_Paginator::factory(Engine_Api::_()->getItemMulti($type, $ids)),
				'counts' => $rawData
			);
		}else{
      if ($period == 'all') {
			  return false;
      } else {
        return array(
				  'paginator' => Zend_Paginator::factory(Engine_Api::_()->getItemMulti($type, $ids)),
				  'counts' => $rawData
			  );
      }
		}
	}
}