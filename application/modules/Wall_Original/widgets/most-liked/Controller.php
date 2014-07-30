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
 */


class Wall_Widget_MostLikedController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer && !$viewer->getIdentity()){
      return $this->setNoRender();
    }
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('like')){
      return $this->setNoRender();
    }


    $support_type = array(
      'page',
      'event',
      'classified',
      'group',
      'user',
      'blog',
      'video',
      'album',
      'quiz',
      'poll',
      'store_product'
    );

    $likeTable = Engine_Api::_()->getDbTable('likes', 'core');

    $select = $likeTable->select()
        ->setIntegrityCheck(false)
        ->from(array('l' => $likeTable->info('name')), new Zend_Db_Expr('l.*, COUNT(*) AS like_count'))
        ->where('resource_type IN (?)', $support_type)
        ->group('resource_type')
        ->group('resource_id')
        ->order('like_count DESC')
        ->limit(10);


    $items = array();
    $like_count = array();

    foreach ($likeTable->fetchAll($select) as $item){
      $items[] = array(
        'type' => $item->resource_type,
        'id' => $item->resource_id,
      );
      $like_count[$item->resource_type  . '_' . $item->resource_id] = $item->like_count;
    }


    if (empty($items)){
      return $this->setNoRender();
    }

    $this->view->items = Engine_Api::_()->wall()->getItems($items);
    $this->view->like_count = $like_count;



  }

}