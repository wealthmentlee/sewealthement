<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Question.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hequestion_Model_Question extends Core_Model_Item_Abstract
{


  protected $_parent_type = 'user';
  protected $_searchTriggers = array('title');
  protected $_parent_is_owner = true;


  public function getType()
  {
    return 'hequestion';
  }

  protected function _delete()
  {

    if ($this->_disableHooks) return;

    $optionTable = Engine_Api::_()->getDbTable('options', 'hequestion');

    foreach ($optionTable->fetchAll(array('question_id = ?' => $this->getIdentity())) as $option){
      $option->delete();
    }

    $voteTable = Engine_Api::_()->getDbTable('votes', 'hequestion');

    foreach ($voteTable->fetchAll(array('question_id = ?' => $this->getIdentity())) as $vote){
      $vote->delete();
    }

    $followTable = Engine_Api::_()->getDbTable('followers', 'hequestion');

    foreach ($followTable->fetchAll(array('question_id = ?' => $this->getIdentity())) as $follow){
      $follow->delete();
    }


    parent::_delete();


  }

  public function isMulti()
  {
    return $this->can_add;
  }

  public function getRichContent()
  {

    $view = Zend_Registry::get('Zend_View');
    $view->question = $this;
    $content = $view->render('application/modules/Hequestion/views/scripts/_question.tpl');


    return $content;


  }

  public function getOptions()
  {
    $optionTable = Engine_Api::_()->getDbTable('options', 'hequestion');

    $select = $optionTable->select()
        ->where('question_id = ?', $this->getIdentity())
        ->order('vote_count DESC');

    return $optionTable->fetchAll($select);

  }


  public function getOptionPaginator()
  {
    $optionTable = Engine_Api::_()->getDbTable('options', 'hequestion');

    $select = $optionTable->select()
        ->where('question_id = ?', $this->getIdentity())
        ->order('vote_count DESC');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(3);

    return $paginator;

  }

  public function getOption($option_id)
  {
    $optionTable = Engine_Api::_()->getDbTable('options', 'hequestion');

    $select = $optionTable->select()
        ->where('question_id = ?', $this->getIdentity())
        ->where('option_id = ?', $option_id);

    return $optionTable->fetchRow($select);
  }


  public function getFollowers()
  {
    $followTable = Engine_Api::_()->getDbTable('followers', 'hequestion');

    $select = $followTable->select()
        ->where('question_id = ?', $this->getIdentity());

    $user_ids = array();

    foreach ($followTable->fetchAll($select) as $follow){
      $user_ids[] = $follow->user_id;
    }

    if (empty($user_ids)){
      return array();
    }

    $userTable = Engine_Api::_()->getDbTable('users', 'user');

    $select = $userTable->select()
        ->where('user_id IN (?)', $user_ids);

    return $followTable->fetchAll($select);

  }

  public function isFollower(User_Model_User $object)
  {
    $followTable = Engine_Api::_()->getDbTable('followers', 'hequestion');

    $select = $followTable->select()
        ->where('question_id = ?', $this->getIdentity())
        ->where('user_id = ?', $object->getIdentity());

    return (bool) $followTable->fetchRow($select);

  }



  public function unvote(User_Model_User $object)
  {
    foreach ($this->getOptions() as $option){
      $option->unvote($object);
    }
  }


  public function getObjectAnswers(User_Model_User $object)
  {

    $optionTable = Engine_Api::_()->getDbTable('options', 'hequestion');
    $voteTable = Engine_Api::_()->getDbTable('votes', 'hequestion');


    $select = $optionTable->select()
        ->from(array('o' => $optionTable->info('name')), new Zend_Db_Expr('o.*'))
        ->join(array('v' => $voteTable->info('name')), 'o.option_id = v.option_id', array())
        ->where('o.question_id = ?', $this->getIdentity())
        ->where('v.user_id = ?', $object->getIdentity())
        ->order('o.vote_count DESC');


    return $optionTable->fetchAll($select);

  }


  public function getObjectAnswersBody(User_Model_User $object)
  {
    $view = Zend_Registry::get('Zend_View');
    $view->answers = $this->getObjectAnswers($object);

    return $view->render('application/modules/Hequestion/views/scripts/_answer.tpl');

  }



  public function canVote($user)
  {
    if (empty($user)){
      return ;
    }
    if (!($user instanceof User_Model_User)){
      return ;
    }
    if (!$user->getIdentity()){
      return ;
    }
    return true;
  }


  public function hasVote(User_Model_User $object)
  {

    $voteTable = Engine_Api::_()->getDbTable('votes', 'hequestion');

    $select = $voteTable->select()
        ->where('question_id = ?', $this->question_id)
        ->where('user_id = ?', $object->getIdentity());

    return (bool) $voteTable->fetchRow($select);

  }

  public function canRemoveLink(User_Model_User $user)
  {

    if (empty($this->parent_type) || empty($this->parent_id)){
      return ;
    }
    $parent = Engine_Api::_()->getItem($this->parent_type, $this->parent_id);
    if (!$parent){
      return ;
    }


    $isAllowedEdit = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('hequestion', $user, 'edit');

    $isOwnerSubject = false;
    if ($parent->isOwner($user) || ($parent->getType() == 'page' && $parent->isTeamMember($user))){
      $isOwnerSubject = true;
    }

    return ($isAllowedEdit || $isOwnerSubject);

  }






  public function getHref($params = array())
  {
    $slug = $this->getSlug();

    $params = array_merge(array(
      'route' => 'hequestion_view',
      'reset' => true,
      'question_id' => $this->getIdentity(),
      'slug' => $slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, $reset);
  }


  public function getSlug($str = null)
  {
    $str = $this->getTitle();
    if( strlen($str) > 32 ) {
      $str = Engine_String::substr($str, 0, 32) . '...';
    }
    $str = preg_replace('/([a-z])([A-Z])/', '$1 $2', $str);
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9-]+/i', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    $str = trim($str, '-');
    if( !$str ) {
      $str = '-';
    }
    return $str;
  }



  // Interfaces

  /**
   * Gets a proxy object for the comment handler
   *
   * @return Engine_ProxyObject
   **/
  public function comments()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('comments', 'core'));
  }


  /**
   * Gets a proxy object for the like handler
   *
   * @return Engine_ProxyObject
   **/
  public function likes()
  {
    return new Engine_ProxyObject($this, Engine_Api::_()->getDbtable('likes', 'core'));
  }







}