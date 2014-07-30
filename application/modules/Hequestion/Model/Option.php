<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Option.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hequestion_Model_Option extends Core_Model_Item_Abstract
{

  protected $_searchTriggers = false;

  public function _delete()
  {

    $question = $this->getParent();

    $voteTable = Engine_Api::_()->getDbTable('votes', 'hequestion');

    foreach ($voteTable->fetchAll(array('option_id = ?' => $this->getIdentity())) as $vote){
      $vote->delete();
      $question->vote_count--;
    }

    $question->save();


    parent::_delete();

  }


  public function getVote(User_Model_User $object)
  {

    $voteTable = Engine_Api::_()->getDbTable('votes', 'hequestion');

    $select = $voteTable->select()
        ->where('question_id = ?', $this->question_id)
        ->where('option_id = ?', $this->option_id)
        ->where('user_id = ?', $object->getIdentity());

    return $voteTable->fetchRow($select);

  }


  public function vote(User_Model_User $object)
  {
    $voteTable = Engine_Api::_()->getDbTable('votes', 'hequestion');

    $vote = $this->getVote($object);


    if (!$vote){



      // vote count
      $this->vote_count++;
      $this->save();



      $question = $this->getParent();
      // if didn't vote
      if (!$voteTable->fetchRow($voteTable->getVoteSelect($question->getIdentity(), $object))){
        $question->vote_count++;
        $question->save();
      }


      $vote = $voteTable->createRow();
      $vote->setFromArray(array(
        'user_id' => $object->getIdentity(),
        'question_id' => $this->question_id,
        'option_id' => $this->option_id,
        'creation_date' => date('Y-m-d H:i:s')
      ));
      $vote->save();

      if (!$question->isMulti()){
        foreach ($question->getOptions() as $option){
          if ($vote->option_id == $option->option_id){
            continue ;
          }
          $option->unvote($object);
        }
      }





    }

    return $vote;

  }


  public function unvote(User_Model_User $object)
  {

    $voteTable = Engine_Api::_()->getDbTable('votes', 'hequestion');

    $vote = $this->getVote($object);
    if ($vote){


      // vote count
      $this->vote_count--;
      $this->save();

      $question = $this->getParent();


      // if didn't vote
      if (!$voteTable->fetchRow($voteTable->getVoteSelect($question->getIdentity(), $object)
          ->where('option_id != ?', $vote->option_id)
      )){
        $question->vote_count--;
        $question->save();
      }

/*      if (!$question->isMulti()){
        foreach ($question->getOptions() as $option){
          $option->unvote($object);
        }
      }*/

      $vote->delete();


    }

  }


  public function getParent()
  {
    return Engine_Api::_()->getItem('hequestion', $this->question_id);
  }



  public function getVoteMembers($object, $params = array())
  {

    $userTable = Engine_Api::_()->getDbTable('users', 'user');
    $memberTable = Engine_Api::_()->getDbTable('membership', 'user');
    $voteTable = Engine_Api::_()->getDbTable('votes', 'hequestion');



    if ($object && $object->getIdentity())
    {
      $select = $userTable->select()
          ->from(array('u' => $userTable->info('name')), new Zend_Db_Expr('u.*'))
          ->join(array('v' => $voteTable->info('name')), 'v.user_id = u.user_id', array())
          ->joinLeft(array('m' => $memberTable->info('name')), 'm.user_id = u.user_id AND m.active = 1 AND m.resource_id = ' . $object->getIdentity(), array())
          ->where('v.question_id = ?', $this->question_id)
          ->where('v.option_id = ?', $this->option_id)
          ->group('u.user_id')
          ->order('m.active DESC')
          ->order('v.creation_date DESC');

      if (isset($params['list_type']) && $params['list_type'] == 'mutual') {
        $select->where('m.active = 1');
      }

    } else {

      $select = $userTable->select()
          ->from(array('u' => $userTable->info('name')), new Zend_Db_Expr('u.*'))
          ->join(array('v' => $voteTable->info('name')), 'v.user_id = u.user_id', array())
          ->where('v.question_id = ?', $this->question_id)
          ->where('v.option_id = ?', $this->option_id)
          ->group('u.user_id')
          ->order('v.creation_date DESC');

    }

    if (!empty($params) && !empty($params['keyword'])){
      $select->where('u.displayname LIKE ? OR u.username LIKE ?', '%'. $params['keyword'] . '%');
    }


    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(3);

    return $paginator;


  }




  public function getHref($params = array())
  {
    $slug = $this->getSlug();

    $params = array_merge(array(
      'route' => 'hequestion_view',
      'reset' => true,
      'question_id' => $this->getParent()->getIdentity(),
      'option_id' => $this->getIdentity(),
      'slug' => $slug,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, $reset);
  }




}