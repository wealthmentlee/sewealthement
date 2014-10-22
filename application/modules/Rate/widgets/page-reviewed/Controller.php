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

class Rate_Widget_PageReviewedController extends Engine_Content_Widget_Abstract
{
  private $item_ids = array();

  public function indexAction()
  {
    $this->view->item_type = $item_type = 'page';

    if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')){
      return $this->setNoRender();
    }

    $tbl = Engine_Api::_()->getDbTable('votes', 'rate');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //$this->view->maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
    $this->view->maxRate = 5; // todo change stars count
    $minVotes = $settings->getSetting('rate.' . $item_type . '.min.votes', 1);
    $maxItems = $settings->getSetting('rate.' . $item_type . '.max.items', 5);
    $this->view->period = $period = $settings->getSetting('rate.' . $item_type . '.period_enabled', true);

    $mostRates = $tbl->fetchMostReviewed($maxItems, $minVotes);

    if (empty($mostRates)) {
      return $this->setNoRender();
    }

    $this->view->all_rates = $this->_prepareRates($mostRates);

    if ($period) {
      $this->view->month_rates = $this->_prepareRates($tbl->fetchMostReviewed($maxItems, $minVotes, 'month'));
      $this->view->week_rates = $this->_prepareRates($tbl->fetchMostReviewed($maxItems, $minVotes, 'week'));
    }

    $pagesTbl = Engine_Api::_()->getDbtable('pages', 'page');
    $select = $pagesTbl->select()->where('page_id IN (?)', $this->item_ids);

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

/*
  public function getCacheKey()
  {
    return Zend_Registry::get('Locale')->toString();
  }
*/
}