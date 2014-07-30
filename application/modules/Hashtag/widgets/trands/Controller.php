<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @Autor      Bolot
 */


class Hashtag_Widget_TrandsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    /**
     * @var $tagsTable Hashtag_Model_DbTable_Tags
     * @var $mapsTable Hashtag_Model_DbTable_Maps
     */
    $tagsTable = Engine_Api::_()->getDbTable('tags', 'hashtag');
    $tName = $tagsTable->info('name');

    $mapsTable = Engine_Api::_()->getDbTable('maps', 'hashtag');
    $mTName = $mapsTable->info('name');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->active_pin = $settings->getSetting('Pinfeed.use_homepage', 0);
    $db = Engine_Api::_()->getDbTable('modules', 'core');
    $select = $db->select()
      ->where('name = ?', 'pinfeed')
      ->where('enabled = ?', 1);

    $this->view->enebled_pin = $db->fetchRow($select);
    //$select->where('creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $select = $tagsTable->select()->from(array('t' => $tName), array('*', 'c' => 'COUNT(*)'))
      ->joinInner(array('m' => $mTName), 'm.map_id = t.map_id', array())
      ->where('m.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL ' . $settings->getSetting('hashtag.period',5) . ' DAY)'))
      ->group('hashtag')->order('c desc')->limit($settings->getSetting('hashtag.count', 5));


    $this->view->tags = $tagsTable->fetchAll($select);
  }

}