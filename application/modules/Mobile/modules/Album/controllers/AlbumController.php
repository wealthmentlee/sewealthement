<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AlbumController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Album_AlbumController extends Core_Controller_Action_Standard
{
  public function init()
  {
    if( !$this->_helper->requireAuth()->setAuthParams('album', null, 'view')->isValid() ) return;
    
    if( 0 !== ($photo_id = (int) $this->_getParam('photo_id')) &&
        null !== ($photo = Engine_Api::_()->getItem('album_photo', $photo_id)) )
    {
      Engine_Api::_()->core()->setSubject($photo);
    }

    else if( 0 !== ($album_id = (int) $this->_getParam('album_id')) &&
        null !== ($album = Engine_Api::_()->getItem('album', $album_id)) )
    {
      Engine_Api::_()->core()->setSubject($album);
    }
  }

  public function viewAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if( !$this->_helper->requireSubject('album')->isValid() ) return;

    $this->view->album = $album = Engine_Api::_()->core()->getSubject();
    if( !$this->_helper->requireAuth()->setAuthParams($album, null, 'view')->isValid() ) return;

    // Prepare params
    $this->view->page = $page = $this->_getParam('page');

    // Prepare data
    if (method_exists($album, 'getCollectiblesPaginator')) {
      $this->view->paginator = $paginator = $album->getCollectiblesPaginator();
    } else {
      $photoTable = Engine_Api::_()->getItemTable('album_photo');
      $this->view->paginator = $paginator = $photoTable->getPhotoPaginator(array(
        'album' => $album,
      ));
    }

    $paginator->setItemCountPerPage(5);
    $paginator->setCurrentPageNumber($page);

    // Do other stuff
    $this->view->mine = true;
    $this->view->can_edit = $this->_helper->requireAuth()->setAuthParams($album, null, 'edit')->checkRequire();
    if( !$album->getOwner()->isSelf(Engine_Api::_()->user()->getViewer()) )
    {
      $album->view_count++;
      $album->save();
      $this->view->mine = false;
    }

  }
  
  public function deleteAction()
  {
    $album = Engine_Api::_()->getItem('album', $this->getRequest()->getParam('album_id'));

    if( !$this->_helper->requireAuth()->setAuthParams($album, null, 'delete')->isValid()) return;

    $this->view->form = $form = new Album_Form_Album_Delete();
		$cancel = $form->getElement('cancel');
		$cancel->setOptions(array('link'=>true, 'href'=>urldecode($this->_getParam('return_url')), 'onclick'=>''));
		$form->removeElement('cancel');
		$group = $form->getDisplayGroup('buttons');
		$group->addElement($cancel);

    if( !$album )
    {
      $message = Zend_Registry::get('Zend_Translate')->_("Album doesn't exists or not authorized to delete");
      return $this->_forward('success' ,'utility', 'mobile', array(
      	'return_url' => $this->_getParam('return_url'),
      	'messages' => Array($message)
    	));
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return;
    }

    $db = $album->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $album->delete();
      
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }


    $message = Zend_Registry::get('Zend_Translate')->_('Album has been deleted.');

		return $this->_forward('success' ,'utility', 'mobile', array(
      'return_url' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'manage'), 'album_general', true),
      'messages' => Array($message)
    ));
  }
}