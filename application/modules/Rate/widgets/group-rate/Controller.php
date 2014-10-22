<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-07-02 19:53 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Widget_GroupRateController extends Engine_Content_Widget_Abstract
{
  private $item_ids = array();

  public function indexAction()
  {
    $this->view->item_type = $item_type = 'group';

    $table = Engine_Api::_()->getDbtable('rates', 'rate');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    //$this->view->maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
    $this->view->maxRate = 5; // todo change stars count
    $maxItems = $settings->getSetting('rate.' . $item_type . '.max.items', 5);
    $minVotes = $settings->getSetting('rate.' . $item_type . '.min.votes', 1);
    $this->view->period = $period = $settings->getSetting('rate.' . $item_type . '.period_enabled', true);

    $mostRatedItems = $table->fetchMostRated($item_type, $maxItems, $minVotes);

    if (empty($mostRatedItems)) {
      return $this->setNoRender();
    }

    $this->view->all_rates = $this->_prepareRates($mostRatedItems);

    if ($period) {
      $this->view->month_rates = $this->_prepareRates($table->fetchMostRated($item_type, $maxItems, $minVotes, 'month'));
      $this->view->week_rates = $this->_prepareRates($table->fetchMostRated($item_type, $maxItems, $minVotes, 'week'));
    }

    $groupsTbl = Engine_Api::_()->getDbtable('groups', 'group');
    $select = $groupsTbl->select()->where('group_id IN (?)', $this->item_ids);

    $items = $groupsTbl->fetchAll($select);
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

  public function getCacheKey()
  {
    return Zend_Registry::get('Locale')->toString();
  }
}