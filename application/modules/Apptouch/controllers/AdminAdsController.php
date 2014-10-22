<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 13.08.13
 * Time: 17:16
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_AdminAdsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $page = $this->_getParam('page', 'user_auth_login');

    $pageTable = Engine_Api::_()->getDbTable('pages', 'apptouch');
    $pageSelect = $pageTable->select()->order('');


    // Get current page
    $this->view->pageObject = $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page)->orWhere('page_id = ?', $page));
    if( null === $pageObject ) {
      $page = 'user_auth_login';
      $pageObject = $pageTable->fetchRow($pageTable->select()->where('name = ?', $page));
      if( null === $pageObject ) {
        throw new Engine_Exception('Home page is missing');
      }
    }
    $this->view->page = $page;
    $this->view->pageObject = $pageObject;
    $this->view->allPages = $allPages = $pageTable->fetchAll($pageSelect);

    $this->view->form = $form = new Apptouch_Form_Admin_Ads_Page($pageObject);
    $form->populate($pageObject->toArray());

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    $params = $form->getValues();
    $form->addNotice($this->view->translate('Your changes have been saved.'));
    $pageObject->adcampaign_id = $params['adcampaign_id'];
    $pageObject->enable_ad = $params['enable_ad'];
    $pageObject->save();
  }

  public function createAction()
  {
      if( isset($_GET['ul']) || isset($_FILES['Filedata']) ) return $this->_forward('upload-photo', null, null, array('format' => 'json'));
      $ad_id = $this->_getParam('id');
      $ad_campaign_id = $this->_getParam('ad_campaign');
      $this->view->form = $form = new Apptouch_Form_Admin_Ads_Ad();
    if($ad = Engine_Api::_()->getItem('core_adcampaign', $ad_campaign_id)){
      $form->populate(array('ad_campaign' => $ad_campaign_id));
    }
    if($ad = Engine_Api::_()->getItem('apptouch_ad', $ad_id)){
      $this->view->ad = $ad;
      $form->setAttrib('class', 'edit-ad');
      $form->setTitle('APPTOUCH_Edit Ad:');
      $form->populate($ad->toArray());
      $form->getElement('ad_campaign')->setLabel('APPTOUCH_Move To Campaign:');
    }
      if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) )
      {
        $params = $form->getValues();
        if(!$ad)
          $ad = Engine_Api::_()->getDbtable('ads', 'apptouch')->createRow();
        $ad->setFromArray($params);
        $ad->save();
        // redirect to manage page for now
        $this->_helper->redirector->gotoRoute(array('action' => 'manage', 'id'=>$this->_getParam('ad_campaign')));
      }
    }

  public function manageAction()
  {
    $this->view->campaign_id = $id = $this->_getParam('id', 0);
    $this->view->campaign = $campaign = Engine_Api::_()->getItem('core_adcampaign', $id);
    $this->view->formFilter = $form = new Engine_Form();
    $form
      ->clearDecorators()
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'search'))
      ->addDecorator('HtmlTag2', array('tag' => 'div', 'class' => 'clear'))
      ;

    $form
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('GET');

    $table = Engine_Api::_()->getDbtable('adcampaigns', 'core');
    $select = $table->select()
      ->order('adcampaign_id DESC');
    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($paginator->getTotalItemCount());

    $adCampaign = array('' => '');
    foreach($paginator as $camp){
      $adCampaign[$camp->getIdentity()] = $camp->name;
    }
    $form->addElement('Select', 'id', array(
      'label' => 'Ad Campaign',
      'required' => true,
      'onchange' => 'this.form.submit(this)',
      'allowEmpty' => false,
      'multiOptions' => $adCampaign,
      'value' => $id
    ));
    if($campaign){
      $table = Engine_Api::_()->getDbtable('ads', 'apptouch');
      $select = $table->select()->where('ad_campaign = ?', $campaign->adcampaign_id);
      $this->view->paginator = $paginator = Zend_Paginator::factory($select);
      $paginator->setCurrentPageNumber($this->_getParam('page'));
    }
    //$paginator->setItemCountPerPage(1);
  }

  public function uploadPhotoAction()
  {
    if( !$this->_helper->requireUser()->checkRequire() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Max file size limit exceeded (probably).");
      return;
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");
      return;
    }

    $values = $this->getRequest()->getPost();
    if( empty($values['Filename']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("No file");
      return;
    }

    if( !isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name']) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid Upload");
      return;
    }
    $table = Engine_Api::_()->getDbtable('adphotos', 'apptouch');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = Engine_Api::_()->user()->getViewer();

      $params = array(
        'owner_type' => 'user',
        'owner_id' => $viewer->getIdentity()
      );
      $photo_id = Engine_Api::_()->getApi('Ad', 'apptouch')->createPhoto($params, $_FILES['Filedata'])->adphoto_id;

      $this->view->status = true;
      $this->view->name = $_FILES['Filedata']['name'];
      $this->view->photo_id = $photo_id;
      $this->view->photo_url = "<a href='' target='_blank'><img src='".Engine_Api::_()->getItem('apptouch_adphoto', $photo_id)->getPhotoUrl()."'/></a>";


      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->error = 'An error occurred.'.$e;
      // throw $e;
      return;
    }
  }

  public function removephotoAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $photo_id= (int) $this->_getParam('photo_id');
    $photo = Engine_Api::_()->getItem('apptouch_adphoto', $photo_id);

    if( !$this->getRequest()->isPost() ) {
      return;
    }

    $db = $photo->getTable()->getAdapter();
    $db->beginTransaction();

    try
    {
      $photo->delete();
      // @todo need to delete it out of storage system
      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }
  public function deleteadAction()
  {
    $this->view->form = $form = new Core_Form_Admin_Ads_Addelete();
    $id = $this->_getParam('ad_id', null);

    if( $id ) {
      $form->ad_id->setValue($id);
    }

    if( $this->getRequest()->isPost() ) {
      $table = Engine_Api::_()->getDbtable('ads', 'apptouch');
      $db = $table->getAdapter();
      $db->beginTransaction();

      try {
        $ad = Engine_Api::_()->getItem('apptouch_ad', $id);
        Engine_Api::_()->getApi('ad', 'apptouch')->deleteAd($ad);
        $db->commit();

        $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => true,
        'parentRefresh' => true,
        'format'=> 'smoothbox',
        'messages' => array(Zend_Registry::get('Zend_Translate')->_("Advertisement Deleted."))
        ));
      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
    }
  }

  public function getAdsAction(){
    if($campaign_id = $this->_getParam('campaign_id')){
      $table = Engine_Api::_()->getDbtable('ads', 'apptouch');
      $select = $table->select()
        ->where('ad_campaign=?', $campaign_id);
      $this->view->ads = $table->fetchAll($select)->toArray();
    };
  }
}
