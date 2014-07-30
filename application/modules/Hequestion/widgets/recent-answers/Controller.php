<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hequestion_Widget_RecentAnswersController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {



    $viewer = Engine_Api::_()->user()->getViewer();

    $select = Engine_Api::_()->getDbTable('questions', 'hequestion')->getLastAnswers($viewer);

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));




    $question_ids = array();
    $voter_ids = array();

    foreach ($paginator as $item){
      $question_ids[] = $item->question_id;
      $voter_ids[] = $item->user_id;
    }



    $voters = array();
    foreach (Engine_Api::_()->getItemMulti('user', $voter_ids) as $item){
      $voters[$item->getIdentity()] = $item;
    }
    $questions = array();
    foreach (Engine_Api::_()->getItemMulti('hequestion', $question_ids) as $item){
      $questions[$item->getIdentity()] = $item;
    }



    $answers = array();
    foreach ($paginator as $item){

      $voter = false;
      if (!empty($voters[$item->user_id])){
        $voter = $voters[$item->user_id];
      }
      $question = false;
      if (!empty($questions[$item->question_id])){
        $question = $questions[$item->question_id];
      }

      if (empty($voter) || empty($question)){
        continue ;
      }

      $answers[] = array(
        'voter' => $voter,
        'question' => $question,
        'answer' => $question->getObjectAnswersBody($voter),
        'vote' => $item
      );
    }



    $this->view->paginator = $paginator;
    $this->view->voters = $voters;
    $this->view->questions = $questions;
    $this->view->answers = $answers;


    if (!$paginator->getTotalItemCount()){
      return $this->setNoRender();
    }

  }
}
