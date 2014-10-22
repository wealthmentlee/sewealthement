<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Offerreviews.php 2012-09-28 19:53 taalay $
 * @author     TJ
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Model_DbTable_Offerreviews extends Engine_Db_Table
{
  protected $_rowClass = 'Rate_Model_Offerreview';

  public function getPaginator($offer_id, $user_id, $page = 1)
  {
    $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(
      array('r' => $this->info('name')),
      new Zend_Db_Expr('r.*, IF(r.user_id=' . (int)$user_id . ',1,0) AS `is_owner`'))
      ->joinLeft(array('v' => $tbl_vote->info('name')), 'v.review_id = r.offerreview_id', 'AVG(v.rating) AS rating')
      ->where('r.offer_id = ?', $offer_id)
      ->group('r.offerreview_id')
      ->order('is_owner DESC')
      ->order('r.creation_date DESC');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);

    return $paginator;
  }

  public function isAllowedPost($offer_id, $viewer)
  {
    /**
     * @var $offer Offers_Model_Offer
     */

    $offer = Engine_Api::_()->getItem('offer', $offer_id);

    if ($offer && $viewer->getIdentity()) {
      $select = $this->select()
        ->where('user_id = ?', $viewer->getIdentity())
        ->where('offer_id = ?', $offer_id);
      $is_post = (bool)$select->query()->rowCount();

      $subscription = $offer->getSubscription($viewer->getIdentity());

      if ($subscription) {
        return true;
      }
    }

    return false;
  }

  public function getScore($item_id)
  {
    $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');
    $is_types = (bool)$tbl_type->select()
      ->where('category_id = ?', 1)
      ->query()
      ->rowCount();

    if (!$is_types) {
      return false;
    }

    $tbl = Engine_Api::_()->getDbTable('votes', 'rate');
    $select = $tbl->select()
      ->from($tbl->info('name'), new Zend_Db_Expr('SUM(rating) AS rating, COUNT(*) AS total'))
      ->where('offer_id = ?', $item_id);

    $row = $tbl->getAdapter()->fetchRow($select);

    $item_score = $count = 0;

    if ($row) {
      $item_score = ($row['rating']) ? round($row['rating'] / $row['total'], 2) : 0;

      $count = $this->select()
        ->where('offer_id = ?', $item_id)
        ->query()
        ->rowCount();
    }

    return array(
      'item_score' => $item_score,
      'count' => $count
    );
  }
}