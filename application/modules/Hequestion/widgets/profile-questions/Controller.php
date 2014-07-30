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



class Hequestion_Widget_ProfileQuestionsController extends Engine_Content_Widget_Abstract
{

  protected $_childCount;

  public function indexAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject();
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }



    $select = Engine_Api::_()->getDbTable('questions', 'hequestion')->getProfileQuestionSelect($subject);


    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));
    $paginator->setCurrentPageNumber( $this->_getParam('page') );



    $this->view->paginator = $paginator;


    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }



    if (!$paginator->getTotalItemCount()){
      return $this->setNoRender();
    }

  }

  public function getChildCount()
  {
    return $this->_childCount;
  }


}
