<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Classified_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('classified', null, 'view')->isValid() ) return;
  }

  // NONE USER SPECIFIC METHODS
  public function indexAction()
  {
    // Check auth
    if( !$this->_helper->requireAuth()->setAuthParams('classified', null, 'view')->isValid() ) return;
    
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
      ->getNavigation('classified_main');

    // Prepare form
    $this->view->form = $form = new Mobile_Form_Search();

    $this->view->user = $user = $this->_getParam('user');
    $form->addElement('Hidden', 'user', array('value' => $user));
    $form->setAction($this->view->url(array(
      'action' => 'index'
    ), 'classified_general', true));

    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('classified', null, 'create')->checkRequire();

    // Process form
    if( $form->isValid($this->getRequest()->getParams()) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }

    // check to see if request is for specific user's listings
    $user_id = $this->_getParam('user');
    if ($user_id) $values['user_id'] = $user_id;

    $this->view->userObj = ($user_id) ? Engine_Api::_()->user()->getUser($user_id) : null;

    $this->view->assign($values);
    
    // items needed to show what is being filtered in browse page
    if (!empty($values['tag'])) $this->view->tag_text = Engine_Api::_()->getItem('core_tag', $values['tag'])->text;
    
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    $classifiedApi = Engine_Api::_()->getApi('core', 'classified');
    $classifiedsTbl = Engine_Api::_()->getDbTable('classifieds', 'classified');

    if (method_exists($classifiedApi, 'getClassifiedsSelect')) {
      $select = $classifiedApi->getClassifiedsSelect($values);
    } else {
      $select = $classifiedsTbl->getClassifiedsSelect($values);
    }

    $this->view->search = $search = $this->_getParam('search');

    if (!empty($search)){
      $select->where('title LIKE ? OR body = ?', '%'.$search.'%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator
        ->setCurrentPageNumber( $this->_getParam('page',1))
        ->setItemCountPerPage(5);

    $this->view->paginator = $paginator;

  }

  public function viewAction()
  {
    $viewer = $this->_helper->api()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->_getParam('classified_id'));
    if( $classified )
    {
      Engine_Api::_()->core()->setSubject($classified);
    }

    // Check auth
    if( !$this->_helper->requireAuth()->setAuthParams($classified, null, 'view')->isValid() ) {
      return;
    }

    // Get navigation
    $this->view->gutterNavigation = $gutterNavigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('classified_gutter');
    
    $can_edit = $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams($classified, null, 'edit')->checkRequire();
    $this->view->allowed_upload = ( $viewer && $viewer->getIdentity()
        && Engine_Api::_()->authorization()->isAllowed('classified', $viewer, 'photo') );

    if( $classified )
    {
      $this->view->owner = $owner = Engine_Api::_()->getItem('user', $classified->owner_id);
      $this->view->viewer = $viewer;

      $classified->view_count++;
      $classified->save();

      $this->view->classified = $classified;
      if ($classified->photo_id){
        $this->view->main_photo = $classified->getPhoto($classified->photo_id);
      }
      // get tags
      $this->view->classifiedTags = $classified->tags()->getTagMaps();
      $this->view->userTags = $classified->tags()->getTagsByTagger($classified->getOwner());

      // get custom field values
      //$this->view->fieldsByAlias = Engine_Api::_()->fields()->getFieldsValuesByAlias($classified);
      // Load fields view helpers
      $view = $this->view;
      $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
      $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($classified);

      // album material
      $this->view->album = $album = $classified->getSingletonAlbum();
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
      $paginator->setCurrentPageNumber($this->_getParam('page', 1));
      $paginator->setItemCountPerPage(100);

      $classifiedApi = Engine_Api::_()->getApi('core', 'classified');
      $classifiedsTbl = Engine_Api::_()->getDbTable('categories', 'classified');
      $classifiedCatsTbl = Engine_Api::_()->getDbtable('categories', 'classified');

      if ($classified->category_id !=0) {
        if (method_exists($classifiedApi, 'getClassifiedsSelect')) {
          $this->view->category = $classifiedApi->getCategory($classified->category_id);
        } else {
          $this->view->category = $classifiedCatsTbl->find($classified->category_id)->current();
        }
      }

      if (method_exists($classifiedApi, 'getUserCategories')) {
        $this->view->userCategories = $classifiedApi->getUserCategories($this->view->classified->owner_id);
      } else {
        $this->view->userCategories = array();
        //draft field error
        //$this->view->userCategories = $classifiedCatsTbl->getUserCategoriesAssoc($this->view->classified->owner_id);
      }

    }
  }

  // USER SPECIFIC METHODS
  public function manageAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    
    // Get navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'mobile')
      ->getNavigation('classified_main');

    $viewer = $this->_helper->api()->user()->getViewer();

    $this->view->can_create = $this->_helper->requireAuth()->setAuthParams('classified', null, 'create')->checkRequire();
    $this->view->allowed_upload = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'photo');

    $this->view->form = $form = new Mobile_Form_Search();

    $this->view->user = $user = $this->_getParam('user');
    $form->addElement('Hidden', 'user', array('value' => $user));
    $form->setAction($this->view->url(array(
      'action' => 'manage'
    ), 'classified_general', true));

    // Process form
    if( $form->isValid($this->getRequest()->getParams()) ) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
    
    //$customFieldValues = $form->getSubForm('custom')->getValues();
    $values['user_id'] = $viewer->getIdentity();

    $this->view->assign($values);

    $classifiedApi = Engine_Api::_()->getApi('core', 'classified');
    $classifiedsTbl = Engine_Api::_()->getDbTable('classifieds', 'classified');

    if (method_exists($classifiedApi, 'getClassifiedsSelect')) {
      $select = $classifiedApi->getClassifiedsSelect($values);
    } else {
      $select = $classifiedsTbl->getClassifiedsSelect($values);
    }

    $this->view->search = $search = $this->_getParam('search');

    if (!empty($search)){
      $select->where('title LIKE ? OR body = ?', '%'.$search.'%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator
        ->setCurrentPageNumber( $this->_getParam('page',1))
        ->setItemCountPerPage(5);

    $this->view->paginator = $paginator;

    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    // maximum allowed classifieds
    $this->view->quota = $quota = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'classified', 'max');
    $this->view->current_count = $paginator->getTotalItemCount();
  }

  public function deleteAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->getRequest()->getParam('classified_id'));
    if( !$this->_helper->requireAuth()->setAuthParams($classified, null, 'delete')->isValid()) return;

    $this->view->form = $form = new Classified_Form_Delete();
    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    if( !$classified )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Classified listing doesn't exist or not authorized to delete");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $classified->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $classified->delete();
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('Your classified listing has been deleted.')),
      'return_url'=>urldecode($this->_getParam('return_url')),
    ));

  }
  
  public function closeAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = $this->_helper->api()->user()->getViewer();
    $classified = Engine_Api::_()->getItem('classified', $this->_getParam('classified_id'));
    if( !Engine_Api::_()->core()->hasSubject('classified') ) {
      Engine_Api::_()->core()->setSubject($classified);
    }
    $this->view->classified = $classified;

    // Check auth
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams($classified, $viewer, 'edit')->isValid() ) {
      return;
    }

    // @todo convert this to post only

    $table = $classified->getTable();
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $classified->closed = $this->_getParam('closed');
      $classified->save();

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'manage'), 'classified_general', true);
  }

}

