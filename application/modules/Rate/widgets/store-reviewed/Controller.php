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

class Rate_Widget_StoreReviewedController extends Engine_Content_Widget_Abstract
{
  private $item_ids = array();

  public function indexAction()
  {
    $this->view->item_type = $item_type = 'page';

    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page') || !Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {
      return $this->setNoRender();
    }
	  
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //$this->view->maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
    $this->view->maxRate = 5; // todo change stars count
    $minVotes = $settings->getSetting('rate.' . $item_type . '.min.votes', 1);
    $maxItems = $settings->getSetting('rate.' . $item_type . '.max.items', 5);
    $this->view->period = $period = $settings->getSetting('rate.' . $item_type . '.period_enabled', true);

    $mostRates = $this->fetchMostReviewed($maxItems, $minVotes);

    if (empty($mostRates)) {
      return $this->setNoRender();
    }

    $this->view->all_rates = $this->_prepareRates($mostRates);
	  
    if ($period) {
      $this->view->month_rates = $this->_prepareRates($this->fetchMostReviewed($maxItems, $minVotes, 'month'));
      $this->view->week_rates = $this->_prepareRates($this->fetchMostReviewed($maxItems, $minVotes, 'week'));
    }

	  $api = Engine_Api::_()->getApi('page', 'store');
    $pagesTbl = Engine_Api::_()->getDbtable('pages', 'page');
    $prefix = $pagesTbl->getTablePrefix();
    $select = $pagesTbl->select()
      ->from($prefix . 'page_pages')
      ->where($prefix . 'page_pages.page_id IN (?)', $this->item_ids);

	  $select = $api->setStoreIntegrity($select);
    $items = $pagesTbl->fetchAll($select);
	  
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
      $rate['object_id'] = $rate['page_id'];
      $items[$rate['page_id']] = $rate;
      $this->item_ids[] = $rate['page_id'];
    }

    return $items;
  }

  public function fetchMostReviewed($limit = 5, $votes = 1, $period = 'all')
  {
	  $api = Engine_Api::_()->getApi('page', 'store');
	  $tbl = Engine_Api::_()->getDbTable('votes', 'rate');
	  $prefix = $tbl->getTablePrefix();
    $tbl_name = $tbl->info('name');
    $select = $tbl->select()
      ->setIntegrityCheck(false)
      ->from( array('t1' => $tbl_name), array('page_id' => 't1.page_id') )
      ->joinLeft( array('t2' => $tbl_name), 't1.page_id = t2.page_id', array(
        'item_score' => 'AVG(t2.rating)',
        'item_count' => 'COUNT(t2.page_id)'))
      ->joinInner($prefix.'page_pages', $prefix.'page_pages.page_id = t1.page_id', array())
      ->group('page_id')
      ->having('item_count >= ?', $votes)
      ->order('item_count DESC')
      ->order('item_score DESC')
      ->limit($limit);

    if ($period == 'month') {
      $select->where('t1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
    } elseif ($period == 'week') {
      $select->where('t1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
    }
	  
	  $select = $api->setStoreIntegrity($select);
    $mostReviewed = $tbl->getAdapter()->fetchAll($select);

    return $mostReviewed;
  }
}