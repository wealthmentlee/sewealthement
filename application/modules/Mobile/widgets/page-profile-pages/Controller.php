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
    
class Mobile_Widget_PageProfilePagesController extends Engine_Content_Widget_Abstract
{
  protected $_childCount;
  
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page')){
      return $this->setNoRender();
    }

    $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('user');
    $this->view->veiwer = $viewer = Engine_Api::_()->user()->getViewer();

    if ($subject->getType() != 'user'){
      return $this->setNoRender();
    }

    if(!$subject->authorization()->isAllowed($viewer, 'view')){
      return $this->setNoRender();
    }

    $table = Engine_Api::_()->getDbtable('membership', 'page');
    $itemTable = Engine_Api::_()->getDbTable('pages', 'page');

    $itName = $itemTable->info('name');
    $mtName = $table->info('name');
    $col = current($itemTable->info('primary'));

    $select = $itemTable->select()
      ->setIntegrityCheck(false)
      ->from($itName)
      ->joinLeft($mtName, "`{$mtName}`.`resource_id` = `{$itName}`.`{$col}`", array('admin_title' => "{$mtName}.title"))
      ->where("`{$mtName}`.`user_id` = ?", $subject->getIdentity())
      ->where("`{$mtName}`.`active` = 1")
      ->where("`{$itName}`.`approved` = 1");

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    
    // Do not render if nothing to show
    if( $paginator->getTotalItemCount() <= 0 ){
      return $this->setNoRender();
    }
    
    $ids = array();
    foreach ($paginator as $page){
      $ids[] = $page->getIdentity();  
    }
    
    $this->view->like_counts = Engine_Api::_()->like()->getLikesCount('page', $ids);
    
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