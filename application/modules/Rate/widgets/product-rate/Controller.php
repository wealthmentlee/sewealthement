<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:53 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Widget_ProductRateController extends Engine_Content_Widget_Abstract
{
  private $item_ids = array();

  public function indexAction()
  {
    $this->view->item_type = $item_type = 'store_product';

    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {
      return $this->setNoRender();
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');

    //$this->view->maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
    $this->view->maxRate = 5; // todo change stars count
    $minVotes = $settings->getSetting('rate.' . $item_type . '.min.votes', 1);
    $maxItems = $settings->getSetting('rate.' . $item_type . '.max.items', 5);
    $this->view->period = $period = $settings->getSetting('rate.' . $item_type . '.period_enabled', true);

    $mostRates = $this->fetchMostRated($maxItems, $minVotes);
    
    if (empty($mostRates)) {
      return $this->setNoRender();
    }

    $this->view->all_rates = $this->_prepareRates($mostRates);

    if ($period) {
      $this->view->month_rates = $this->_prepareRates($this->fetchMostRated($maxItems, $minVotes, 'month'));
      $this->view->week_rates = $this->_prepareRates($this->fetchMostRated($maxItems, $minVotes, 'week'));
    }

    $productTbl = Engine_Api::_()->getDbtable('products', 'store');
    $select = $productTbl->select()->where('product_id IN (?)', $this->item_ids);

    $items = $productTbl->fetchAll($select);

    $this->view->items = array();
    foreach ($items as $item) {
      $this->view->items[$item->getIdentity()] = $item;
    }

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);
    $this->getElement()->setAttrib('class', 'rate_widget_theme_' . $this->view->activeTheme());
  }

  private function _prepareRates($rates)
  {
    if (!$rates) {
      return array();
    }

    $items = array();

    foreach ($rates as $rate) {
      $items[$rate['object_id']] = $rate;
      $items[$rate['object_id']]['item_score'] = ($rate['total_score'] && $rate['rate_count'])
        ? $rate['total_score'] / $rate['rate_count']
        : 0;

      $this->item_ids[] = $rate['object_id'];
    }

    return $items;
  }

	private function fetchMostRated($limit = 5, $votes = 1, $period = 'all')
  {
    if (!$limit) {
      $limit = 5;
    }

	  $tbl = Engine_Api::_()->getDbtable('rates', 'rate');
	  $productTbl = Engine_Api::_()->getDbTable('products', 'store');
	  $prefix = $productTbl->getTablePrefix();

    $select_opts = array(
      'object_id',
      'rate_count' => new Zend_Db_Expr('COUNT(rate_id)'),
      'total_score' => new Zend_Db_Expr('SUM(score)'),
      'avg_score' => new Zend_Db_Expr('AVG(score)')
    );

    $select = $tbl->select()
      ->setIntegrityCheck(false)
      ->from(array('r' => $tbl->info('name')), $select_opts)
      ->joinInner($prefix.'store_products', $prefix.'store_products.product_id = r.object_id', array())
      ->group('object_id')
      ->having('rate_count >= ?', $votes, 'INTEGER')
      ->order('avg_score DESC')
      ->order('rate_count DESC')
      ->limit($limit);

    if ($period == 'month') {
      $select->where('rated_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
    } elseif ($period == 'week') {
      $select->where('rated_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
    }

	  $select = $productTbl->setStoreIntegrity($select);
	  $select
	    ->where("object_type = ?", 'store_product', 'STRING');
    return $tbl->getAdapter()->fetchAll($select);
  }
}