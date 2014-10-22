<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Plugin_Core
{
  public function onUserDeleteBefore($rate)
  {
    $payload = $rate->getPayload();
    if ($payload instanceof User_Model_User) {
      // Delete rates
      $ratesTable = Engine_Api::_()->getDbTable('rates', 'rate');
      $ratesTable->delete(array('user_id = ?' => $payload->getIdentity()));

      // Delete Reviews
      $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
      $rows = $tbl->fetchAll($tbl->select()->where('user_id = ?', $payload->getIdentity()));
      foreach ($rows as $row){ $row->delete(); }

    }
  }

  public function removePage($event)
  {
    $payload = $event->getPayload();
	  $page = $payload['page'];

    $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
    $rows = $tbl->fetchAll($tbl->select()->where('page_id = ?', $page->getIdentity()));
    foreach ($rows as $row){ $row->delete(); }

  }

  public function typeDelete($event){

    $payload = $event->getPayload();
    $category_id = $payload['option_id'];

    $tbl = Engine_Api::_()->getDbTable('types', 'rate');
    $tbl->delete(array(
      'category_id = ?' => $category_id
    ));

  }

  public function typeCreate($event){

    $payload = $event->getPayload();

    if ($payload['option']['module'] == 'store') {
      return;
    }

    if ($payload['option']){

      Engine_Api::_()->getDbTable('types', 'rate')->createRow(array(
        'category_id' => $payload['option']['option_id'],
        'label' => 'Rate',
        'order' => 1
      ))->save();
    }

  }

}