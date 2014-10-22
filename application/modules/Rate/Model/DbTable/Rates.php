<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Rates.php 2010-07-02 19:53 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Model_DbTable_Rates extends Engine_Db_Table
{
  protected $_rowClass = "Rate_Model_Rate";

   public function fetchUserRate($object_type, $object_id, $user_id)
  {
    if (!$object_type || !$object_id || !$user_id) {
      return false;
    }
    
    $select = $this->select()
      ->where('object_type = ?', $object_type, 'STRING')
      ->where('object_id = ?', $object_id, 'INTEGER')
      ->where('user_id = ?', $user_id, 'INTEGER');

    return $this->fetchRow($select);
  }

  public function fetchRateInfo($object_type, $object_id, $period = 'all')
  {
    if (!$object_type || !$object_id) {
      return false;
    }

    $select_opts = array(
      'rate_count' => new Zend_Db_Expr('COUNT(rate_id)'),
      'total_score' => new Zend_Db_Expr('SUM(score)')
    );

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from($this->info('name'), $select_opts)
      ->where("object_type = ?", $object_type, 'STRING')
      ->where("object_id = ?", $object_id, 'INTEGER');

    if ($period == 'month') {
      $select->where('rated_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 MONTH)'));
    } elseif ($period == 'week') {
      $select->where('rated_date > ?', new Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 WEEK)'));
    }

    return $this->getAdapter()->fetchRow($select);
  }

  public function fetchMostRated($object_type, $limit = 5, $votes = 1, $period = 'all')
  {
    if (!$object_type) {
      return false;
    }
    
    if (!$limit) {
      $limit = 5;
    }
    $table = $this->getObjectTable($object_type);
    if (!$table || (!$primary_keys = $table->info('primary')) || !is_array($primary_keys)) {
      return false;
    }

    $primary = array_shift($primary_keys);

    $select_opts = array(
      'object_id',
      'rate_count' => new Zend_Db_Expr('COUNT(rate_id)'),
      'total_score' => new Zend_Db_Expr('SUM(score)'),
      'avg_score' => new Zend_Db_Expr('AVG(score)')
    );

    $select = $this->select()
      ->setIntegrityCheck(false)
      ->from($this->info('name'), $select_opts)
      ->join($table->info('name'), "{$table->info('name')}.{$primary} = {$this->info('name')}.object_id")
      ->where("object_type = ?", $object_type, 'STRING')
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
   
    return $this->getAdapter()->fetchAll($select);
  }

  public function getObjectTable($object_type)
  {
    try {
      $table = Engine_Api::_()->getItemTable($object_type);
    }
    catch (Exception $e) {
      throw $e;
      $table = false;
    }

    return $table;
  }
}