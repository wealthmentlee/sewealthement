<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Votes.php 2010-07-02 19:53 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Model_DbTable_Votes extends Engine_Db_Table
{
  public function fetchMostReviewed($limit = 5, $votes = 1, $period = 'all')
  {
    $tbl_name = $this->info('name');
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from( array('t1' => $tbl_name), array('page_id' => 't1.page_id') )
      ->joinLeft( array('t2' => $tbl_name), 't1.page_id = t2.page_id', array(
        'item_score' => 'AVG(t2.rating)',
        'item_count' => 'COUNT(t2.page_id)'))
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

    $mostReviewed = $this->getAdapter()->fetchAll($select);

    return $mostReviewed;
  }

  public function fetchMostOfferReviewed($limit = 5, $votes = 1, $period = 'all')
  {
    $tbl_name = $this->info('name');
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from( array('t1' => $tbl_name), array('offer_id' => 't1.offer_id') )
      ->joinLeft( array('t2' => $tbl_name), 't1.offer_id = t2.offer_id', array(
        'item_score' => 'AVG(t2.rating)',
        'item_count' => 'COUNT(t2.offer_id)'))
      ->group('offer_id')
      ->having('item_count >= ?', $votes)
      ->order('item_count DESC')
      ->order('item_score DESC')
      ->limit($limit);

    if ($period == 'month') {
      $select->where('t1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
    } elseif ($period == 'week') {
      $select->where('t1.creation_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
    }

    $mostReviewed = $this->getAdapter()->fetchAll($select);

    return $mostReviewed;
  }

  public function getAllVotes()
  {
    $typesTbl = Engine_Api::_()->getDbTable('types', 'rate');
    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from(array('v'=>$this->info('name')))
      ->join(array('t'=>$typesTbl->info('name')), 't.type_id=v.type_id');

    return $this->fetchAll($select);
  }
}