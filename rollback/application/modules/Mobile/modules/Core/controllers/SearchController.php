<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SearchController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Core_SearchController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $searchApi = Engine_Api::_()->getApi('search', 'core');
    //$viewer = $this->_helper->api()->user()->getViewer();

    // check public settings
    $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
    if( !$require_check ) {
      if( !$this->_helper->requireUser()->isValid() ) return;
    }

    // Prepare form
    $this->view->form = $form = new Core_Form_Search();

    // Get available types
    $availableTypes = $searchApi->getAvailableTypes();
    if( is_array($availableTypes) && count($availableTypes) > 0 ) {
      $options = array();
      foreach( $availableTypes as $index => $type ) {
        $options[$type] = strtoupper('ITEM_TYPE_' . $type);
      }
      $form->type->addMultiOptions($options);
    } else {
      $form->removeElement('type');
    }

    // Check form validity?
    $values = array();
    if( $form->isValid($this->_getAllParams()) ) {
      $values = $form->getValues();
    }
    
    $this->view->query = $query = (string) @$values['query'];
    $this->view->type = $type = (string) @$values['type'];
    $this->view->page = $page = (int) $this->_getParam('page');
    if( $query )
    {
      $this->view->paginator = $searchApi->getPaginator($query, $type);
      $this->view->paginator->setCurrentPageNumber($page);
    }
  }
}