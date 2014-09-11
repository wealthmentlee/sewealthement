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


class Wall_Widget_PeopleKnowController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer && !$viewer->getIdentity()){
      return $this->setNoRender();
    }

    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->view->feedOnly = $feedOnly = $request->getParam('feedOnly');

    if( $feedOnly ) {
      $this->getElement()->removeDecorator('Title');
      $this->getElement()->removeDecorator('Container');
    }

    $userTable = Engine_Api::_()->getDbTable('users', 'user');
    $membershipTable = Engine_Api::_()->getDbTable('membership', 'user');



    $select = $userTable->select()
        ->from(array('u' => $userTable->info('name')), new Zend_Db_Expr('u.*'))
        ->join(array('m' => $membershipTable->info('name')), 'm.resource_id = u.user_id', array())
        ->where('m.active = 1')
        ->where('m.user_id = ?', $viewer->getIdentity())
        ;


    $friend_ids = array();

    foreach ($userTable->fetchAll($select) as $item){
      $friend_ids[] = $item->user_id;
    }


    if (empty($friend_ids)){
      return $this->setNoRender();
    }

    $select = $userTable->select()
        ->from(array('u' => $userTable->info('name')), new Zend_Db_Expr('u.*'))
        ->join(array('m' => $membershipTable->info('name')), 'm.resource_id = u.user_id', array())
        ->where('m.active = 1')
        ->where('m.user_id IN (?)', $friend_ids)
        ->where('u.user_id NOT IN (?)', $friend_ids)
        ->where('u.user_id != ?', $viewer->getIdentity())
        ->group('u.user_id')
        ->order('u.member_count DESC');



    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber( $request->getParam('page') );



    if (!$paginator->getTotalItemCount()){
      return $this->setNoRender();
    }


  }

}