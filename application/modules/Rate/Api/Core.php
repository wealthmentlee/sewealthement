<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-07-02 19:27 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Api_Core extends Core_Api_Abstract
{
  public function isSupportedPlugin($plugin_name)
  {
    $supported_plugins = array('user', 'group', 'event', 'quiz', 'blog', 'album_photo', 'page', 'article', 'store_product');

    return in_array($plugin_name, $supported_plugins);
  }

  public function getItemVoters($parameters)
  {
    $table = Engine_Api::_()->getDbTable('rates', 'rate');
    $userTable = Engine_Api::_()->getItemTable('user');

    $ratesTable = $table->info('name');
    $usersTable = $userTable->info('name');

    if (isset($parameters['list_type']) && $parameters['list_type'] == 'mutual') {

      $viewer = Engine_Api::_()->user()->getViewer();
      $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');
      $membership_table = $membershipTable->info('name');

      $viewer = Engine_Api::_()->user()->getViewer();

      $select = $userTable->select()
        ->setIntegrityCheck(false)
        ->from($usersTable)
        ->join($ratesTable, "$ratesTable.user_id = $usersTable.user_id", array())
        ->joinLeft($membership_table, "$ratesTable.user_id = $membership_table.user_id", array())
        ->where("$ratesTable.object_id = ?", $parameters['item_id'], 'INTEGER')
        ->where("$ratesTable.object_type = ?", $parameters['item_type'], 'STRING')
        ->where('engine4_user_membership.resource_id = ?', $viewer->getIdentity())
        ->where('engine4_user_membership.resource_approved = 1')
        ->where('engine4_user_membership.user_approved = 1');

    } else {

      $select = $userTable->select()
        ->setIntegrityCheck(false)
        ->from($usersTable)
        ->join($ratesTable, "$ratesTable.user_id = $usersTable.user_id", array())
        ->where("$ratesTable.object_id = ?", $parameters['item_id'], 'INTEGER')
        ->where("$ratesTable.object_type = ?", $parameters['item_type'], 'STRING');

    }

    if (isset($parameters['keyword']) && $parameters['keyword']) {
      $select->where("$usersTable.displayname LIKE ?", '%' . $parameters['keyword'] . '%', 'STRING');
    }

    return Zend_Paginator::factory($select);
  }

  public function getPageCategories()
  {
    $tbl_pagemeta = Engine_Api::_()->fields()->getTable('page', 'meta');
    $select = $tbl_pagemeta->select()
      ->from($tbl_pagemeta->info('name'), 'field_id')
      ->where('type = ?', 'profile_type', 'STRING')
      ->limit(1);
    $field_id = $tbl_pagemeta->getAdapter()->fetchOne($select);

    $tbl_pageoption = Engine_Api::_()->fields()->getTable('page', 'options');
    $select = $tbl_pageoption->select()
      ->where('field_id = ?', $field_id);

    return $tbl_pageoption->fetchAll($select);
  }

  public function getPageTypes($page_id)
  {
    $tbl_field = Engine_Api::_()->fields()->getTable('page', 'values');
    $select = $tbl_field->select()
      ->from($tbl_field->info('name'), 'value')
      ->where('item_id = ?', $page_id);

    $category_id = $tbl_field->getAdapter()->fetchOne($select);

    $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');
    $select = $tbl_type->select()
      ->where('category_id = ?', $category_id)
      ->order('order');

    return $tbl_type->fetchAll($select);
  }

  public function getOfferCategories()
  {
    /**
     * @var $table Offers_Model_DbTable_Categories
     */
    $table = Engine_Api::_()->getDbTable('categories', 'offers');
    return $table->getCategoriesAssoc();
  }

  public function getOfferTypes($offer_id)
  {
    $offer = Engine_Api::_()->getItem('offer', $offer_id);
    if (!$offer) {
      return array();
    }

    $tbl_type = Engine_Api::_()->getDbTable('offertypes', 'rate');
    $select = $tbl_type->select()
      ->where('category_id = ?', $offer->category_id)
      ->order('order');

    return $tbl_type->fetchAll($select);
  }

  public function getComments($page = null)
  {
    $row = Engine_Api::_()->core()->getSubject();
    if (null !== $page) {
      $commentSelect = $row->comments()->getCommentSelect();
      $commentSelect->order('comment_id ASC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber($page);
      $comments->setItemCountPerPage(10);
    }
    else {
      $commentSelect = $row->comments()->getCommentSelect();
      $commentSelect->order('comment_id DESC');
      $comments = Zend_Paginator::factory($commentSelect);
      $comments->setCurrentPageNumber(1);
      $comments->setItemCountPerPage(4);
    }

    return $comments;
  }

  public function isAllowRemoveReview($page_id, $viewer)
  {
    $page = Engine_Api::_()->getDbTable('pages', 'page')
      ->findRow($page_id);

    if ($page && $viewer->getIdentity()) {
      $is_permission = Engine_Api::_()->getApi('settings', 'core')
        ->getSetting('rate.reviewteamremove', 1);

      $is_team = $page->isTeamMember($viewer);
      return ($is_permission && $is_team);
    }
    return false;
  }
}