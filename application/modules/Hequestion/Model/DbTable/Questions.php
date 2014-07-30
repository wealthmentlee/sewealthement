<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Questions.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hequestion_Model_DbTable_Questions extends Engine_Db_Table
{

  protected $_rowClass = 'Hequestion_Model_Question';



  public function getFriendsQuestionSelect(User_Model_User $object)
  {

    $userTable = Engine_Api::_()->getDbTable('users', 'user');
    $memberTable = Engine_Api::_()->getDbTable('membership', 'user');

    $select = $this->select()
        ->setIntegrityCheck(false)
        ->from(array('q' => $this->info('name')), new Zend_Db_Expr('q.*'))
        ->join(array('u' => $userTable->info('name')), 'q.user_id = u.user_id', array())
        ->join(array('m' => $memberTable->info('name')), 'u.user_id = m.user_id AND m.active = 1', array())
        ->where('m.resource_id = ?', $object->getIdentity())
        ->where('m.active = 1')
    ;

    return $select;

  }

  public function getRecentQuestionSelect()
  {

    $select = $this->select()
        ->order('creation_date DESC');

    return $select;

  }

  public function getPopularQuestionSelect()
  {

    $select = $this->select()
        ->order('vote_count DESC');

    return $select;

  }


  public function getQuestionsPaginator()
  {
    return Zend_Paginator::factory($this->getRecentQuestionSelect());
  }

  public function getManageQuestionsPaginator(User_Model_User $user)
  {

    $select = $this->select()
        ->where('user_id = ?', $user->getIdentity())
        ->order('creation_date DESC');


    return Zend_Paginator::factory($select);
  }


  public function getProfileQuestionSelect(Core_Model_Item_Abstract $subject)
  {

    if ($subject instanceof User_Model_User){
      $select = $this->select()
          ->where('user_id = ?', $subject->getIdentity())
          ->order('question_id DESC');
    } else {
      $select = $this->select()
          ->where('parent_type = ?', $subject->getType())
          ->where('parent_id = ?', $subject->getIdentity())
          ->order('question_id DESC');
    }

    return $select;
  }


  public function getLastAnswers(User_Model_User $user)
  {
    $optionTable = Engine_Api::_()->getDbTable('options', 'hequestion');
    $voteTable = Engine_Api::_()->getDbTable('votes', 'hequestion');
    $allowTable = Engine_Api::_()->getDbTable('allow', 'authorization');

    /**
     * @var $apiAuth Authorization_Api_Core
     */


    $select = $optionTable->select()
        ->from(array('o' => $optionTable->info('name')), new Zend_Db_Expr('v.*'))
        ->join(array('v' => $voteTable->info('name')), 'o.option_id = v.option_id', array())

        // show all of everyone privacy
        ->join(array('a' => $allowTable->info('name')), 'v.question_id = a.resource_id AND a.resource_type = "hequestion" AND a.action = "view" AND a.role = "everyone" AND a.value IN ('.Authorization_Api_Core::LEVEL_ALLOW.', '.Authorization_Api_Core::LEVEL_MODERATE.')', array())

        ->group('v.question_id')
        ->order('v.vote_id DESC');


    return $optionTable->fetchAll($select);




  }



}