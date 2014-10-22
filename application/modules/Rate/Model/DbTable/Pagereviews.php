<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Reviews.php 2010-07-02 19:53 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Model_DbTable_Pagereviews extends Engine_Db_Table
{
  protected $_rowClass = 'Rate_Model_Pagereview';

  public function getPaginator($page_id, $user_id, $page = 1){

    $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');

    $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(
            array('r' => $this->info('name')),
            new Zend_Db_Expr('r.*, IF(r.user_id='.(int)$user_id.',1,0) AS `is_owner`'))
        ->joinLeft(array('v' => $tbl_vote->info('name')), 'v.review_id = r.pagereview_id', 'AVG(v.rating) AS rating')
        ->where('r.page_id = ?', $page_id)
        ->group('r.pagereview_id')
        ->order('is_owner DESC')
        ->order('r.creation_date DESC');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);

    return $paginator;

  }

  public function isAllowedPost($page_id, $viewer){

    if ($viewer->getIdentity()){
      $is_permission = Engine_Api::_()->getDbtable('permissions', 'authorization')
            ->getAllowed('rate', $viewer->level_id, 'reviewcreate');
      $select = $this->select()
          ->where('user_id = ?', $viewer->getIdentity())
          ->where('page_id = ?', $page_id);
      $is_post = (bool)$select->query()->rowCount();
      return ($is_permission && !$is_post);
    }
    return false;
  }

  public function getScore($item_id){

      // is review types
      $tbl_field = Engine_Api::_()->fields()->getTable('page', 'values');
      $select = $tbl_field->select()
          ->from($tbl_field->info('name'), 'value')
          ->where('item_id = ?', $item_id);

      $category_id = $tbl_field->getAdapter()->fetchOne($select);

      $tbl_type = Engine_Api::_()->getDbTable('types', 'rate');
      $is_types = (bool)$tbl_type->select()
          ->where('category_id = ?', $category_id)
          ->query()
          ->rowCount();
      if (!$is_types){ return false; }

      $tbl = Engine_Api::_()->getDbTable('votes', 'rate');
      $select = $tbl->select()
          ->from($tbl->info('name'), new Zend_Db_Expr('SUM(rating) AS rating, COUNT(*) AS total'))
          ->where('page_id = ?', $item_id);

      $row = $tbl->getAdapter()->fetchRow($select);

      $item_score = $count = 0;

      if ($row){

        $item_score = ($row['rating']) ? round($row['rating'] / $row['total'], 2) : 0;

        $count = $this->select()
          ->where('page_id = ?', $item_id)
          ->query()
          ->rowCount();
      }

      return array(
        'item_score' => $item_score,
        'count' => $count
      );
  }

  public function getReviewsPaginator($params = array())
  {
    $votesTbl = Engine_Api::_()->getDbTable('votes', 'rate');
    $pagesTbl = Engine_Api::_()->getDbTable('pages', 'page');

    $pagereviewsTblName = $this->info('name');
    $select = $this->select()
      ->setIntegrityCheck(false);

    if (isset($params['sort'])) {
			switch ($params['sort']) {
				case 'recent' :
					$select
            ->from(array('pr'=>$this->info('name')))
            ->join(array('v'=>$votesTbl->info('name')), 'v.review_id = pr.pagereview_id', array('v.vote_id', 'v.review_id', 'v.rating', 'AVG(v.rating) AS avg_rating', 'COUNT(v.vote_id) AS count_rates'))
            ->join(array('p'=>$pagesTbl->info('name')), 'p.page_id = v.page_id', array('p.displayname', 'p.url', 'p.photo_id'))
            ->group('pr.pagereview_id')
						->where("pr.creation_date IN (SELECT MAX(creation_date) FROM {$pagereviewsTblName} GROUP BY page_id)");
					break;
				case 'rated' :

          $firstQuery = $this->select()
            ->setIntegrityCheck(false)
            ->from(array('v1' => $votesTbl->info('name')), array('v1.review_id',  'v1.page_id',  'v1.rating',  'AVG(v1.rating)  AS  avg_rating',  'COUNT(v1.vote_id)  AS  count_rates'))
            ->group('v1.review_id')
            ->group('v1.page_id')
            ->order('avg_rating DESC')
            ->order('count_rates DESC');

          $select
            ->from(array('v2' => $firstQuery), array('v2.*', 'COUNT(v2.page_id) as count_reviews'))
            ->join(array('pr' => $this->info('name')), 'pr.pagereview_id = v2.review_id')
            ->join(array('p' => $pagesTbl->info('name')), 'p.page_id  =  pr.page_id', array('p.displayname',  'p.url',  'p.photo_id'))
            ->group('v2.page_id')
            ->order('avg_rating  DESC')
            ->order('count_reviews DESC')
            ->order('count_rates  DESC');
					break;
			}
		}

    if (isset($params['search']) && $params['search']) {
      $select->where("pr.title LIKE '%{$params['keyword']}%'");
    }
    if (isset($params['category']) && $params['category']) {
      $select->join(array('fv' => 'engine4_page_fields_values'), "fv.item_id=p.page_id AND fv.value={$params['category']}");
    }

    $select->order('pr.creation_date DESC');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($params['itemsCountPerPage']);
    $paginator->setCurrentPageNumber($params['page']);

    return $paginator;
  }
}