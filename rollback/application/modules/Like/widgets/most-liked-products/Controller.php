<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-07-22 16:05 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Widget_MostLikedProductsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
	  if (!Engine_Api::_()->getDbtable('modules', 'hecore')->isModuleEnabled('store')) {
			$this->setNoRender($this);
			return;
		}

    $this->view->widget = 'most_liked';
    $this->view->item_type = $item_type = 'store_product';

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $ipp = $settings->getSetting('like.store_product_count', 9);
    $this->view->period = $period = $settings->getSetting('like.store_product_period', 1);

		$data = $this->getMostLiked($item_type, $ipp);

		if (!$data) {
			$this->setNoRender();
			
			return ;
		}

    if ($period) {
      $week_paginator = $this->getMostLiked($item_type, $ipp, 'week');
      $this->view->week_likes = $week_likes = $week_paginator['paginator'];
      $this->view->week_counts = $week_paginator['counts'];
      if ($week_likes->getTotalItemCount()) {
        $this->view->week_likes->setItemCountPerPage($ipp);
      }

      $month_paginator = $this->getMostLiked($item_type, $ipp, 'month');
      $this->view->month_likes = $month_likes = $month_paginator['paginator'];
      $this->view->month_counts = $month_paginator['counts'];
      if ($month_likes->getTotalItemCount()) {
        $this->view->month_likes->setItemCountPerPage($ipp);
      }
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

		/**
		 * @var $productsTbl Store_Model_DbTable_Products
		 * @var $table Core_Model_DbTable_Likes
		 * @var $select Zend_Db_Table_Select;
		 */

		$productsTbl = Engine_Api::_()->getDbTable('products', 'store');
		$table = Engine_Api::_()->getDbTable('likes', 'core');
		$prefix = $table->getTablePrefix();

		$select = $table->select()
			->from(array('like' => $table->info('name')), array('like.resource_id', 'like_count' => 'COUNT(*)'))
			->joinLeft($prefix.'store_products', 'like.resource_id = '.$prefix.'store_products.product_id', array())
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

		$select = $productsTbl->setStoreIntegrity($select);

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

  /*public function getCacheKey()
  {
    return Zend_Registry::get('Locale')->toString();
  }*/
}