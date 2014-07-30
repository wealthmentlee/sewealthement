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



class Hequestion_Widget_FriendQuestionsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();

    $select = Engine_Api::_()->getDbTable('questions', 'hequestion')->getFriendsQuestionSelect($viewer);


    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));



    $this->view->paginator = $paginator;

    if (!$paginator->getTotalItemCount()){
      return $this->setNoRender();
    }

  }
}
