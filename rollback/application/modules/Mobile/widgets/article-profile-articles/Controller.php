<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 12:40:00 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Widget_ArticleProfileArticlesController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('article')){
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

    if( !($subject instanceof User_Model_User) ) {
      return $this->setNoRender();
    }    
    
    $this->view->items_per_page = $max = $this->_getParam('max', 5);
    $this->view->show_page_details = $this->_getParam('details', 0);

    $this->view->paginator = $paginator = Engine_Api::_()->article()->getPublishedArticlesPaginator(array(
      'order' => 'creation_date',
      'user_id' =>  Engine_Api::_()->core()->getSubject()->getIdentity(),
      'limit' => $this->view->items_per_page,
    ));

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
    
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}