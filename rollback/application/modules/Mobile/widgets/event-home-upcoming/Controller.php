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
    
class Mobile_Widget_EventHomeUpcomingController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event')){
      return $this->setNoRender();
    }
    // Don't render this if not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    $eventTable = Engine_Api::_()->getItemTable('event');
    $eventTableName = $eventTable->info('name');
    $type = $this->_getParam('type');
    
    // Show nothing
    if( $type == '2' && !$viewer->getIdentity() ) {
      return $this->setNoRender();
    }

    // Show member upcoming events
    else if( $type == '2' || ($type == '0' && $viewer->getIdentity()) ) {
      $eventMembership = Engine_Api::_()->getDbtable('membership', 'event');
      $select = $eventMembership->getMembershipsOfSelect($viewer);
    }

    // Show all upcoming events
    else {
      $select = $eventTable->select()
        ->where('search = ?', 1);
    }

    $select
      ->where("`{$eventTableName}`.`endtime` > FROM_UNIXTIME(?)", time())
      //->where("`{$eventTableName}`.`starttime` < FROM_UNIXTIME(?)", time() + (86400 * 14))
      ->order("starttime ASC");

    // Make paginator
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 5));

    // Do not render if nothing to show and not viewer
    if( $paginator->getTotalItemCount() <= 0 ) {
      return $this->setNoRender();
    }

    // Check to make sure we have a title?
    if( '' == $this->getElement()->getTitle() ) {
      $this->getElement()->setTitle('Upcoming Events');
    }
  }
}