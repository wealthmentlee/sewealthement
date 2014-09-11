<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Widget_BlogProfileBlogsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('blog')){
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

    $blogApi = $paginator = Engine_Api::_()->getApi('core', 'blog');
    $blogsTbl = Engine_Api::_()->getDbTable('blogs', 'blog');

    // Get paginator
    if (method_exists($blogApi, 'getBlogsPaginator')) {
      $this->view->paginator = $paginator = $blogApi->getBlogsPaginator(array(
        'orderby' => 'creation_date',
        'draft'  => '0',
        'user_id' =>  Engine_Api::_()->core()->getSubject()->getIdentity(),
      ));
    } else {
      $this->view->paginator = $paginator = $blogsTbl->getBlogsPaginator(array(
        'orderby' => 'creation_date',
        'draft'  => '0',
        'user_id' =>  Engine_Api::_()->core()->getSubject()->getIdentity(),
      ));
    }

    $this->view->paginator->setItemCountPerPage(5);
    $this->view->paginator->setCurrentPageNumber(1);

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->items_per_page = $settings->getSetting('blog_page', 10);
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}