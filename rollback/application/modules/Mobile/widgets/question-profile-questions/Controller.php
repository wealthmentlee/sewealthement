<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-16 10:11:40 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Mobile_Widget_QuestionProfileQuestionsController extends Engine_Content_Widget_Abstract
{
   
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('question')){
      return $this->setNoRender();
    }

    // Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }
    if( !Engine_Api::_()->authorization()->isAllowed('question', $viewer, 'view') ) {
      return $this->setNoRender();
    }
    
    $this->view->categories = Engine_Api::_()->question()->getCategories();
    // Get paginator
    $this->view->paginator = $paginator = Engine_Api::_()->question()->getQuestionPaginator(array(
      'orderby' => 'creation_date',
      'user_id' =>  Engine_Api::_()->core()->getSubject()->getIdentity(),
    ));
    //$this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator->setCurrentPageNumber(1);


    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->question_page;
    $paginator->setItemCountPerPage($items_per_page);
    $this->view->items_per_page = $items_per_page;

    //Answers
    $this->view->paginator_answer = $paginator_answer = Engine_Api::_()->question()->getAnswerPaginator(array(
      'user_id' =>  Engine_Api::_()->core()->getSubject()->getIdentity(),
    ));
    //$this->view->paginator->setItemCountPerPage(10);
    $this->view->paginator_answer->setCurrentPageNumber(1);

    // Do not render if nothing to show
    if( ($paginator_answer->getTotalItemCount() + $paginator->getTotalItemCount()) <= 0 ) {
      return $this->setNoRender();
    }

    $paginator_answer->setItemCountPerPage($items_per_page);
  }
}