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
    
class Mobile_Widget_AlbumProfileAlbumsController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('album')){
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

    $albumApi = $paginator = Engine_Api::_()->getApi('core', 'album');
    $albumsTbl = Engine_Api::_()->getDbTable('albums', 'album');

    // Get paginator
    if (method_exists($albumApi, 'getAlbumPaginator')) {
      $this->view->paginator = $paginator = $albumApi->getAlbumPaginator(array('owner' => $subject, 'search' => 1));
    } else {
      $this->view->paginator = $paginator = $albumsTbl->getAlbumPaginator(array('owner' => $subject, 'search' => 1));
    }

    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Add count to title if configured
    if( $this->_getParam('titleCount', false) && $paginator->getTotalItemCount() > 0 ) {
      $this->_childCount = $paginator->getTotalItemCount();
    }
		
    //$paginator->setItemCountPerPage(1);
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->items_per_page = $settings->getSetting('album_page', 15);
  }

  public function getChildCount()
  {
    return $this->_childCount;
  }
}