<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: HegiftController.php 03.02.12 12:21 Ulan T $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_HegiftController extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    $action = $this->_getParam('action', false);
    if( $action != 'approve' && $action != 'active') {
      /**
       * @var $giftsTbl Hegift_Model_DbTable_Gifts
       */

      $giftsTbl = Engine_Api::_()->getDbTable('gifts', 'hegift');
      $gifts = $giftsTbl->getGifts(array('enabled' => true, 'date' => true));

      foreach ($gifts as $gift) {
        $gift->starttime = date("Y-m-d H:i:s", mktime(1,0,0, date("m", strtotime($gift->starttime)), date("d", strtotime($gift->starttime)), date("Y", strtotime($gift->starttime))+1));
        $gift->endtime = date("Y-m-d H:i:s", mktime(1,0,0, date("m", strtotime($gift->endtime)), date("d", strtotime($gift->endtime)), date("Y", strtotime($gift->endtime))+1));
        $gift->save();
      }
    }
  }

  public function indexIndexAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     * @var $gift Hegift_Model_Gift
     */
    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');

    $page = $this->_getParam('page', 1);
    $category_id = $this->_getParam('category_id', 0);
    $sort = $this->_getParam('sort', 'recent');

    $values = array(
      'sort' => $sort,
      'page' => $page,
      'category_id' => $category_id,
      'ipp' => 10,
      'amount' => true,
      'photo' => true,
      'enabled' => true,
      'status' => 1
    );

    if ( $this->_getParam('search', false) ) {
      $values['title'] = $this->_getParam('search');
    }

    $gifts = $gifts = $table->getGifts($values);

    $count = $gifts->getTotalItemCount();
    $storage = Engine_Api::_()->storage();
    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $this->_getParam('user_id', 0);

    $form = $this->getSearchForm();
    $form->setMethod('get');
    $form->getElement('search')->setValue($this->_getParam('search'));

    $paginatorPages = $gifts->getPages();
    $data = array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',
    );
    $data['items'] = array();
    foreach($gifts as $item) {
      $data['items'][] = array(
        'title' => $item->getTitle(),
        'descriptions' => array(
          $item->credits ? $this->view->translate("HEGIFT_%s credit", $this->view->locale()->toNumber($item->credits)) : $this->view->translate('HEGIFT_Free')
        ),
        'href' => ($viewer && $viewer->getIdentity()) ? $this->view->url(array('action' => 'send', 'gift_id' => $item->getIdentity()), 'hegift_general') : '',
        'photo' => $item->getPhotoUrl('thumb.normal')
      );
    }

    $this->add($this->component()->itemSearch($form))
      ->add($this->component()->customComponent('itemList', $data))
      ->add($this->component()->navigation('hegift_main', true), -1)
//      ->add($this->component()->paginator($gifts))
      ->renderContent();
  }

  public function indexManageAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Recipients
     * @var $paginator Zend_Paginator
     * @var $viewer User_Model_User
     */

    if( !$this->_helper->requireUser()->isValid() )
      return $this->redirect($this->view->url(array('action' => 'manage'), 'hegift_general', true));

    $table = Engine_Api::_()->getDbTable('recipients', 'hegift');
    $action_name = $this->_getParam('action_name', 'received');

    $page = $this->_getParam('page', 1);
    $viewer = Engine_Api::_()->user()->getViewer();
    $paginator = $table->getPaginator(array('user_id' => $viewer->getIdentity(), 'action_name' => $action_name, 'page' => $page, 'ipp' => 10));

    $active_recipient_id = $this->getActiveRecipientId();
    $received_gifts_count = $table->getPaginator(array('user_id' => $viewer->getIdentity(), 'action_name' => 'received'))->getTotalItemCount();
    $sent_gifts_count = $table->getPaginator(array('user_id' => $viewer->getIdentity(), 'action_name' => 'sent'))->getTotalItemCount();
    $storage = Engine_Api::_()->storage();

    $description = $this->view->translate('HEGIFT_sent you this gift ') .'<b>';
    if( $action_name == 'sent' ) {
      $description = $this->view->translate('HEGIFT_received from you this gift ') .'<b>';
    }

    $paginatorPages = $paginator->getPages();
    $data = array(
      'listPaginator' => true,
      'pageCount' => $paginatorPages->pageCount,
      'next' => @$paginatorPages->next,
      'paginationParam' => 'page',
    );
    $data['items'] = array();
    foreach($paginator as $rs) {
      $user = $rs->getUser($action_name);
      $gift = $rs->getGift();

      $des = $description . $rs->getPrivacy().'</b> <br>' .
        $this->view->translate('HEGIFT_Sent %s ', $this->view->timestamp($rs->send_date));

      if( $rs->getMessage() ) {
        $des = $des . '<br><i>' . $rs->getMessage() . '</i>';
      }

      $options = array();
      if ( $rs->approved && $action_name == 'received') {
        $options[] = array(
          'label' => $this->view->translate('HEGIFT_Decline'),
          'attrs' => array(
            'href' => $this->view->url(array('action'=>'approve', 'recipient_id' => $rs->getIdentity(), 'value' => 0), 'hegift_general', true),
            'class' => 'buttonlink'
          )
        );
        if ($active_recipient_id != $rs->getIdentity()) {
          $options[] = array(
            'label' => $this->view->translate('HEGIFT_Active Gift'),
            'attrs' => array(
              'href' => $this->view->url(array('action'=>'active', 'recipient_id' => $rs->getIdentity()), 'hegift_general', true),
              'class' => 'buttonlink'
            )
          );

          if ($gift->isGeneral()) {
            $options[] = array(
              'label' => $this->view->translate('HEGIFT_Send Gift'),
              'attrs' => array(
                'href' => '',
                'class' => 'buttonlink'
              )
            );
          }
        } else {
          $options[] = array(
            'label' => $this->view->translate('HEGIFT_Disactive'),
            'attrs' => array(
              'href' => $this->view->url(array('action'=>'active', 'recipient_id' => $rs->getIdentity()), 'hegift_general', true),
              'class' => 'buttonlink'
            )
          );
          if ($gift->isGeneral()) {
            $options[] = array(
              'label' => $this->view->translate('HEGIFT_Send Gift'),
              'attrs' => array(
                'href' => '',
                'class' => 'buttonlink'
              )
            );
          }
          $options[] = array(
            'label' => $this->view->translate('HEGIFT_Check here'),
            'attrs' => array(
              'href' => $viewer->getHref(),
              'class' => 'buttonlink'
            )
          );
        }
      }

      $url = $this->view->url(array('action' => $gift->getTypeName(), 'gift_id' => $gift->getIdentity(), 'sender_id' => $rs->subject_id), 'hegift_own', true);
      if( $action_name == 'sent' ) {
        $url = $this->view->url(array('action' => $gift->getTypeName(), 'gift_id' => $gift->getIdentity(), 'reciever_id' => $rs->object_id), 'hegift_own', true);
      }

      $data['items'][] = array(
        'title' => $user->getTitle(),
        'descriptions' => array($des),
        'href' => $url,
        'photo' => $gift->getPhotoUrl('thumb.normal'),
        'manage' => $options,
        'attrsA' => array('data-rel' => 'dialog'),
      );
    }

    $quick = $this->getManageNavigation();

    $this
      ->setPageTitle($this->view->translate('Inbox/Sent'))
      ->add($this->component()->html($this->view->translate('APPTOUCH_HEGIFT_'.strtoupper($action_name).'_GIFTS_DESC')))
      ->add($this->component()->customComponent('itemList', $data))
//      ->add($this->component()->paginator($paginator))
      ->add($this->component()->navigation('hegift_main', true), -1)
      ->add($this->component()->quickLinks($quick))
      ->renderContent();
  }

  public function indexSendAction()
  {
    $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));

    if(!Engine_Api::_()->user()->getViewer()->getIdentity()){
      return $this->redirect($this->view->url(array(), 'default', true));
    }
    if( !$gift || !$gift->getIdentity() ) {
      return $this->redirect($this->view->url(array(), 'hegift_general', true));
    }
    $html = $this->getContactsForm( $this->_getParam('text', false), $this->_getParam('page', 1), $gift );

    if( !$this->getRequest()->isPost() ) {
      $this->addPageInfo('contentTheme', 'd');
      $this->add($this->component()->subjectPhoto($gift))
        ->add($this->component()->html($html))
        ->add($this->component()->navigation('hegift_main', true), -1)
        ->renderContent();
      return;
    }

    $values = array();
    $sender = Engine_Api::_()->user()->getViewer();
    if (!$sender->getIdentity()) {
      return $this->redirect($this->view->url(array(), 'hegift_general'));
    }

    $translate = Zend_Registry::get('Zend_Translate');
    $notificationTable = Engine_Api::_()->getDbTable('notifications', 'activity');

    if (!$gift) {
      $this->view->message = $translate->_('HEGIFT_Gift is not found');
      return $this->redirect($this->view->url(array(), 'hegift_general'));
    } else {
      $values['gift_id'] = $gift->getIdentity();
    }

    if ($gift->owner_id) {
      if ($sender->getIdentity() != $gift->owner_id) {
        $this->view->message = $translate->_('HEGIFT_This is not your Gift');
        return $this->redirect($this->view->url(array(), 'hegift_general'));
      }
      if ($gift->isSent()) {
        $this->view->message = $translate->_('HEGIFT_Gift has already sent');
        return $this->redirect($this->view->url(array(), 'hegift_general'));
      }
      if ($gift->getStatus()) {
        $this->view->message = $translate->_('HEGIFT_Gift has already sent from you. Please reload this page.');
        return $this->redirect($this->view->url(array(), 'hegift_general'));
      }
    }

    $recipients = explode(',', $this->_getParam('uids', ''));
    array_pop($recipients);

    if (!count($recipients)) {
      $this->view->message = $translate->_('HEGIFT_You didn\'t select the recipient.');
      return $this->redirect($this->view->url(array(), 'hegift_general'));
    }

    $balance = Engine_Api::_()->getItem('credit_balance', $sender->getIdentity());
    if (!$balance) {
      $this->view->message = $translate->_('HEGIFT_Not enough credits to send a gift.');
      return $this->redirect($this->view->url(array(), 'hegift_general'));
    }

    $credits = count($recipients)*$gift->credits;
    if ($credits > $balance->current_credit) {
      $this->view->message = $translate->_('HEGIFT_Not enough credits to send a gift.');
      return $this->redirect($this->view->url(array(), 'hegift_general'));
    }

    $values['message'] = trim(strip_tags($this->_getParam('message', '')));
    $values['privacy'] = (int)$this->_getParam('privacy', 1);
    if ($values['privacy']) {
      $values['privacy'] = 1;
    }
    $values['subject_id'] = $sender->getIdentity();
    $values['send_date'] = new Zend_Db_Expr("NOW()");

    $table = Engine_Api::_()->getDbTable('recipients', 'hegift');

    $counter = 0;
    $db = $table->getAdapter();
    $db->beginTransaction();

    if ($gift->owner_id == $sender->getIdentity() && $gift->type == 3) {
      $values['approved'] = 0;

      // Sending gift
      try {
        foreach($recipients as $recipient) {
          if ($table->checkGiftForUser($recipient, $values['gift_id'])) {
            continue;
          }
          $values['object_id'] = $recipient;
          $row = $table->createRow();
          $row->setFromArray($values);
          $row->save();
          $counter++;
        }

        $gift->temporaryPay($counter);

        $db->commit();
      } catch (Exception $e) {
        $db->rollBack();
        throw $e;
      }

      Engine_Api::_()->getDbtable('jobs', 'core')->addJob('gift_video_encode', array(
        'gift_id' => $gift->getIdentity(),
      ));

      $this->view->result = 1;
      $this->view->message = $translate->_('HEGIFT_Gift will be sent only when the video will be ready. If the video is an error, we will refund all the credits');

      $task = Engine_Api::_()->hegift()->getTask('core');
      $class = $task->plugin;
      $manualHook = new $class($task);
      $manualHook->execute();
      return $this->redirect($this->view->url(array(), 'hegift_general'));
    }

    // Sending gift
    try {
      foreach($recipients as $recipient) {
        if ($table->checkGiftForUser($recipient, $values['gift_id'])) {
          continue;
        }
        $values['object_id'] = $recipient;
        $row = $table->createRow();
        $row->setFromArray($values);
        $row->save();
        $counter++;
        //activity feed
        if ($values['privacy']) {
          $action = Engine_Api::_()->getDbTable('actions', 'activity')->addActivity($sender, Engine_Api::_()->getItem('user', $recipient), 'sent_gift', null, array('is_mobile' => true));
          if( $action ) {
            Engine_Api::_()->getDbTable('actions', 'activity')->attachActivity($action, $gift);
          }
        }
        //send notification
        $notificationTable->addNotification(Engine_Api::_()->getItem('user', $recipient), $sender, Engine_Api::_()->getItem('user', $recipient), 'send_gift', array(
          'action' => $this->view->url(array('action' => 'manage'), 'hegift_general', true),
          'label' => $translate->_('HEGIFT_here')
        ));
      }

      $gift->payOff($counter);
      $gift->updateGift($counter);

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->message = $translate->_('HEGIFT_Gift successfully sent.');

    return $this->redirect($this->view->url(array(), 'hegift_general', true));
  }

  public function indexActiveAction()
  {
    if( !$this->_helper->requireUser()->isValid() )
      return $this->redirect($this->view->url(array('action' => 'manage'), 'hegift_general', true));
    $viewer = Engine_Api::_()->user()->getViewer();
    $recipient_id = $this->_getParam('recipient_id', 0);
    $recipient = Engine_Api::_()->getItem('hegift_recipient', $recipient_id);
    if (!$recipient->getIdentity()) {
      return $this->redirect($this->view->url(array('action' => 'manage'), 'hegift_general', true));
    }

    if (!$this->indexCheckGift($recipient->getGift(), $recipient)) {
      return $this->redirect($this->view->url(array('action' => 'manage'), 'hegift_general', true));
    }

    $active_recipient_id = $this->getActiveRecipientId();

    $value = $recipient->getIdentity();

    if ($active_recipient_id == $value) {
      $value = 0; // unset activated photo
    }

    $this->setSetting($viewer, 'active_gift', $value);
    return $this->redirect($this->view->url(array('action' => 'manage'), 'hegift_general', true));
  }

  public function indexApproveAction()
  {
    if( !$this->_helper->requireUser()->isValid() )
      return $this->redirect($this->view->url(array('action' => 'manage'), 'hegift_general', true));
    $value = $this->_getParam('value', 1);
    $viewer = Engine_Api::_()->user()->getViewer();
    $recipient_id = $this->_getParam('recipient_id', 0);
    if (!$recipient_id) {
      return $this->redirect($this->view->url(array('action' => 'manage'), 'hegift_general', true));
    }
    $recipient = Engine_Api::_()->getItem('hegift_recipient', $recipient_id);
    if (!$this->indexCheckGift($recipient->getGift(), $recipient)) {
      return $this->redirect($this->view->url(array('action' => 'manage'), 'hegift_general', true));
    }

    $active_recipient_id = $this->getActiveRecipientId();

    if ($active_recipient_id == $recipient_id && $value == 0) {
      $this->setSetting($viewer, 'active_gift', 0);
    }

    $recipient->approved = $value;
    $recipient->save();
    return $this->redirect('refresh');
  }


  public function tempInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if( !$this->_helper->requireUser()->isValid() )
      return $this->redirect($this->view->url(array(), 'hegift_general', true));

    $task = Engine_Api::_()->hegift()->getTask();
    $class = $task->plugin;
    $manualHook = new $class($task);
    $manualHook->execute();
  }

  public function tempIndexAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     * @var $viewer User_Model_User
     */
    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $viewer = Engine_Api::_()->user()->getViewer();
    $page = $this->_getParam('page', 1);
    $paginator = $table->getGifts(array('owner_id' => $viewer->getIdentity(), 'page' => $page, 'sent_count' => true));

    if (!$paginator->getTotalItemCount()) {
      return $this->redirect($this->view->url(array('action' => 'mine'), 'hegift_own', true));
    }

    $this->addPageInfo('contentTheme', 'd');
    $this->add($this->component()->html($this->view->translate('HEGIFT_Temporary Gifts Description')))
      ->add($this->component()->itemList($paginator, 'tempGiftList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->add($this->component()->navigation('hegift_main', true), -1)
      ->renderContent();
  }

  public function tempDeleteAction()
  {
    $translate = Zend_Registry::get('Zend_Translate');
    $gift = Engine_Api::_()->getItem('gift', $this->getRequest()->getParam('gift_id'));
    $form = new Hegift_Form_Delete();
    $viewer = Engine_Api::_()->user()->getViewer();

    if( !$gift ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Gift doesn't exist or not authorized to delete");
      return $this->redirect($this->view->url(array(), 'hegift_temp', true));
    }

    if ($gift->owner_id) {
      if ($viewer->getIdentity() != $gift->owner_id) {
        $this->view->message = $translate->_('HEGIFT_This is not your Gift');
        return $this->redirect($this->view->url(array(), 'hegift_temp', true));
      }
      if ($gift->isSent()) {
        $this->view->message = $translate->_('HEGIFT_Gift has already sent. You cannot to delete!');
        return $this->redirect($this->view->url(array(), 'hegift_temp', true));
      }
      if ($gift->getStatus()) {
        $this->view->message = $translate->_('HEGIFT_Gift has already sent from you. You cannot to delete! Please reload this page.');
        return $this->redirect($this->view->url(array(), 'hegift_temp', true));
      }
    }

    if( !$this->getRequest()->isPost() ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if ($gift->owner_id != $viewer->getIdentity()) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return $this->redirect($this->view->url(array(), 'hegift_temp', true));
    }

    $db = $gift->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $gift->delete();

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your gift has been deleted.');
    return $this->redirect($this->view->url(array(), 'hegift_temp', true));
  }


  public function ownInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if( !$this->_helper->requireUser()->isValid() )
      return $this->redirect($this->view->url(array(), 'hegift_temp', true));

    $this->action = $this->_getParam('action');
    $this->addPageInfo('contentTheme', 'd');
  }

  public function ownIndexAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $type = $this->_getParam('type', '');
    $photo_price = $settings->getSetting('hegift.photo.credits', 50);
    $audio_price = $settings->getSetting('hegift.audio.credits', 80);
    $video_price = $settings->getSetting('hegift.video.credits', 100);

    if( $type == 0 ) {
      $this->view->message = $this->view->translate('HEGIFT_Have to install ffmpeg, contact with administrator of server.');
    }

    $data = array();
    $data['items'] = array();
    $data['items'][] = array(
      'title' => $this->view->translate('HEGIFT_Send photo gift.') . ', ' . $this->view->translate("HEGIFT_%s credit", $this->view->locale()->toNumber($photo_price)),
      'descriptions' => array($this->view->translate('DESC_Create Photo Gift and Send')),
      'href' => $this->view->url(array('action'=>'create', 'type' => 'photo'), 'hegift_own', true),
      'photo' => 'application/modules/Hegift/externals/images/big_gift_photo.png',
      'counter' =>''
    );
    $data['items'][] = array(
      'title' => $this->view->translate('HEGIFT_Send audio gift.') . ', ' . $this->view->translate("HEGIFT_%s credit", $this->view->locale()->toNumber($audio_price)),
      'descriptions' => array($this->view->translate('DESC_Create Audio Gift and Send')),
      'href' => $this->view->url(array('action'=>'create', 'type' => 'audio'), 'hegift_own', true),
      'photo' => 'application/modules/Hegift/externals/images/big_gift_audio.png',
      'counter' =>''
    );
    $data['items'][] = array(
      'title' => $this->view->translate('HEGIFT_Send video gift.') . ', ' . $this->view->translate("HEGIFT_%s credit", $this->view->locale()->toNumber($video_price)),
      'descriptions' => array($this->view->translate('DESC_Create Video Gift and Send')),
      'href' => $this->view->url(array('action'=>'create', 'type' => 'video'), 'hegift_own', true),
      'photo' => 'application/modules/Hegift/externals/images/big_gift_video.png',
      'counter' =>''
    );

    $title = '<div style="font-weight:bold;">'.$this->view->translate('HEGIFT_Create and Send Own Gift.') . '</div>' .
      $this->view->translate('HEGIFT_You can create and send Own Gift. Gifts are photo, audio, and video.');

    $this
      ->setPageTitle($this->view->translate('My Gifts'))
      ->add($this->component()->html($title))
      ->add($this->component()->customComponent('itemList', $data))
      ->add($this->component()->navigation('hegift_main', true), -1)
      ->renderContent();
  }

  public function ownMineAction()
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $type = $this->_getParam('type', '');
    $photo_price = $settings->getSetting('hegift.photo.credits', 50);
    $audio_price = $settings->getSetting('hegift.audio.credits', 80);
    $video_price = $settings->getSetting('hegift.video.credits', 100);

    if( $type == 0 ) {
      $this->view->message = $this->view->translate('HEGIFT_Have to install ffmpeg, contact with administrator of server.');
    }

    $data = array();
    $data['items'] = array();
    $data['items'][] = array(
      'title' => $this->view->translate('HEGIFT_Send photo gift.') . ', ' . $this->view->translate("HEGIFT_%s credit", $this->view->locale()->toNumber($photo_price)),
      'descriptions' => array($this->view->translate('DESC_Create Photo Gift and Send')),
      'href' => $this->view->url(array('action'=>'create', 'type' => 'photo'), 'hegift_own', true),
      'photo' => 'application/modules/Hegift/externals/images/big_gift_photo.png',
      'counter' =>''
    );
    $data['items'][] = array(
      'title' => $this->view->translate('HEGIFT_Send audio gift.') . ', ' . $this->view->translate("HEGIFT_%s credit", $this->view->locale()->toNumber($audio_price)),
      'descriptions' => array($this->view->translate('DESC_Create Audio Gift and Send')),
      'href' => $this->view->url(array('action'=>'create', 'type' => 'audio'), 'hegift_own', true),
      'photo' => 'application/modules/Hegift/externals/images/big_gift_audio.png',
      'counter' =>''
    );
    $data['items'][] = array(
      'title' => $this->view->translate('HEGIFT_Send video gift.') . ', ' . $this->view->translate("HEGIFT_%s credit", $this->view->locale()->toNumber($video_price)),
      'descriptions' => array($this->view->translate('DESC_Create Video Gift and Send')),
      'href' => $this->view->url(array('action'=>'create', 'type' => 'video'), 'hegift_own', true),
      'photo' => 'application/modules/Hegift/externals/images/big_gift_video.png',
      'counter' =>''
    );

    $title = '<div style="font-weight:bold;">'.$this->view->translate('HEGIFT_Create and Send Own Gift.') . '</div>' .
      $this->view->translate('HEGIFT_You can create and send Own Gift. Gifts are photo, audio, and video.');

    $this->add($this->component()->html($title))
      ->add($this->component()->customComponent('itemList', $data))
      ->add($this->component()->navigation('hegift_main', true), -1)
      ->renderContent();
  }

  public function ownPhotoAction()
  {
    $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));

    $sender_id = $this->_getParam('sender_id', 0);
    $reciever_id = $this->_getParam('reciever_id', 0);


    if (!$this->checkGift($gift, $sender_id, $reciever_id)) {
      $this->view->message = 'Invalid data or gift doesn\'t exist';
      return $this->redirect('parentRefresh');
    }

    $html = '<div class="hegift_view_gift">' . $gift->getTitle() . '<br>' . $this->view->itemPhoto($gift, "thumb.profile") . '</div>';

    $this->add($this->component()->html($html))
      ->renderContent();
  }

  public function ownAudioAction()
  {
    $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));
    $file_id = $gift->file_id;

    if (!$this->checkGift($gift, $this->_getParam('sender_id', 0))) {
      $this->view->message = 'Invalid data or gift doesn\'t exist';
      return $this->redirect('parentRefresh');
    }

    if( !empty($file_id) ) {
      $storage_file = Engine_Api::_()->getItem('storage_file', $file_id);
      if( $storage_file ) {
        $data = array(
          'items' => array(
            array(
              'title' => $storage_file->name,
              'href' => $storage_file->map(),
              'playcount' => 0
            )
          )
        );

        $this->add($this->component()->html($gift->getTitle()))
          ->add($this->component()->customComponent('playlist', $data))
          ->add($this->component()->mediaControls())
          ->renderContent();

      }
    }
  }

  public function ownVideoAction()
  {
    /**
     * @var $gift Hegift_Model_Gift
     */

    $gift = Engine_Api::_()->getItem('gift', $this->_getParam('gift_id', 0));

    if (!$this->checkGift($gift, $this->_getParam('sender_id', 0))) {
      $this->view->message = 'Invalid data or gift doesn\'t exist';
      return $this->redirect('parentRefresh');
    }

    if ($gift->status != 1) {
      $this->view->message = 'Video is not encoded yet, encoding will begin only after sending. If you have sent gift, please wait...';
      return $this->redirect('parentRefresh');
    }

    $file_id = $gift->file_id;

    if( !empty($file_id) ) {
      $storage_file = Engine_Api::_()->getItem('storage_file', $file_id);
      if( $storage_file ) {
        $video_location = $storage_file->map();

        $videoFormat = array();
        $videoFormat['flashObject'] = "<object class=\"flowplayer\" width=\"480\" height=\"386\" type=\"application/x-shockwave-flash\"
        data=\"".Zend_Registry::get('StaticBaseUrl')."externals/flowplayer/flowplayer-3.1.5.swf\"><param value=\"true\" name=\"allowfullscreen\">
          <param value=\"always\" name=\"allowscriptaccess\">
          <param value=\"high\" name=\"quality\">
          <param value=\"transparent\" name=\"wmode\">
          <param value=\"config={'clip':{'url':'" . $video_location . "','autoPlay':false,'duration':'" . '' . "','autoBuffering':true},'plugins':{'controls':{'background':'#000000','bufferColor':'#333333','progressColor':'#444444','buttonColor':'#444444','buttonOverColor':'#666666'}},'canvas':{'backgroundColor':'#000000'}}\" name=\"flashvars\">
        </object>";

        $this->add($this->component()->html($gift->getTitle()))
          ->add($this->component()->customComponent('video', $videoFormat))
          ->renderContent();
      }
    }
  }

  public function ownCreateAction()
  {
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     * @var $gift Hegift_Model_Gift
     */

    $type = $this->_getParam('type', '');
    $ffmpeg_path = Engine_Api::_()->getApi('settings', 'core')->video_ffmpeg_path;

    if (empty($type) || ($type == 'video' && !$ffmpeg_path)) {
      return $this->redirect($this->view->url(array('action'=>'mine', 'type' => $type), 'hegift_own', true));
    }

    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $form = new Hegift_Form_Create(array('type' => $type));
    $form->removeElement($type);

    $label = $this->view->translate('Add Photo');
    $order = 1;
//    $desc = $this->view->translate('HEGIFT_Add Photo DESC');

    if( $type == 'audio' ) {
      $label = $this->view->translate('Add Audio');
      $order = 2;
//      $desc = $this->view->translate('HEGIFT_Add Audio DESC');
    } elseif($type == 'video') {
      $label = $this->view->translate('Add Video');
      $order = 2;
//      $desc = $this->view->translate('HEGIFT_Add Video DESC');
    }

    $form->addElement('File', $type, array(
      'order' => $order,
      'label' => $label,
      'allowEmpty' => false,
      'required' => true,
//      'description' => $desc,
    ));
    $form->getDecorator('Description')->setOption('escape', false);

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->add($this->component()->navigation('hegift_main', true), -1)
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->add($this->component()->navigation('hegift_main', true), -1)
        ->renderContent();
      return;
    }

    $values = $form->getValues();

    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $db = $table->getAdapter();
    $db->beginTransaction();

    $gift = $table->createRow();

    try {
      $gift->setFromArray($values);
      $gift->type = ($type == 'video') ? 3 : (($type == 'audio') ? 2 : 1);
      $gift->creation_date = new Zend_Db_Expr('NOW()');
      $gift->owner_id = Engine_Api::_()->user()->getViewer()->getIdentity();
      $gift->credits = $settings->getSetting('hegift.'.$type.'.credits', ($type == 'video') ? 100 : (($type == 'audio') ? 80 : 60));
      $gift->amount = 0;
      $gift->save();

      $params = array(
        'parent_type' => 'gift',
        'parent_id' => $gift->getIdentity(),
      );
      if ($type == 'photo') {
        $photo = $this->getPicupFiles('photo');
        if( !empty($values['photo']) ) {
          $gift->setPhoto($form->photo);
        } else {
         $gift->setPhoto($photo[0]);
        }
      } elseif ($type == 'audio') {
        $audio = $this->getPicupFiles('audio');
        if( !empty($values['audio']) ) {
          $audio_file = Engine_Api::_()->storage()->create($form->audio->getFileName(), $params);
          $gift->setAudio($audio_file->getIdentity());
        } else {
          $audio_file = Engine_Api::_()->storage()->create($audio[0], $params);
          $gift->setAudio($audio_file->getIdentity());
        }
        $photo = $this->getPicupFiles('photo');
        if( !empty($values['photo']) ) {
          $gift->setPhoto($form->photo);
        } else {
          $gift->setPhoto($photo[0]);
        }
      } elseif ($type == 'video') {
        $video = $this->getPicupFiles('video');
        if( !empty($values['video']) ) {
          $video_file = Engine_Api::_()->storage()->create($form->video->getFileName(), $params);
          $gift->setVideo($video_file->getIdentity());
        } else {
          $video_file = Engine_Api::_()->storage()->create($video[0], $params);
          $gift->setVideo($video_file->getIdentity());
        }

        $photo = $this->getPicupFiles('photo');
        if( !empty($values['photo']) ) {
          $gift->setPhoto($form->photo);
        } else {
          $gift->setPhoto($photo[0]);
        }
      }

      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->message = $this->view->translate('Gift successfully created');
    return $this->redirect($this->view->url(array('action' => 'select-send', 'gift_id' => $gift->getIdentity()), 'hegift_own', true));
  }

  public function ownSelectSendAction()
  {
    /**
     * @var $gift Hegift_Model_Gift
     */

    $gift_id = $this->_getParam('gift_id', 0);
    $gift = Engine_Api::_()->getItem('gift', $gift_id);
    $type = $gift->getTypeName();
    if (!$gift || $gift->isSent()) {
      return $this->redirect($this->view->url(array('action' => 'mine'), 'hegift_own'));
    }

    $des = $this->view->translate('HEGIFT_Your %s gift successfully created, here you can select '.
      'friends witch want to send this gift, then Send, for this click button below. If you decide not to send a'.
      ' gift and closed the page, do not worry your gift to be kept in %s', $gift->getTypeName(), $this->view->htmlLink($this->view->url(array(), 'hegift_temp', true), $this->view->translate('HEGIFT_temporary storage'), array('target' => '_blank')));

    $button = $this->view->htmlLink($this->view->url(array('action' => 'send', 'gift_id' => $gift_id), 'hegift_general', true), $this->view->translate('HEGIFT_Select Friends and Send Gift'), array('data-role' => 'button'));
    $this->add($this->component()->subjectPhoto($gift))
      ->add($this->component()->html($des))
      ->add($this->component()->html($button))
      ->add($this->component()->navigation('hegift_main', true), -1)
      ->renderContent();
  }


  public function tempGiftList( Core_Model_Item_Abstract $item )
  {
    $description = '<div style="color:red">' . $this->view->translate('HEGIFT_will be removed %s', $this->view->timestamp($item->getRemovingDate())) . '</div>';

    $options = array();
    $options[] = array(
      'label' => $this->view->translate('HEGIFT_Send Gift'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'send', 'gift_id' => $item->getIdentity()), 'hegift_general'),
        'class' => 'buttonlink'
      )
    );
    $options[] = array(
      'label' => $this->view->translate('HEGIFT_Delete Gift'),
      'attrs' => array(
        'href' => $this->view->url(array('action' => 'delete', 'gift_id' => $item->getIdentity()), 'hegift_temp', true),
        'class' => 'buttonlink',
        'data-rel' => 'dialog'
      )
    );

    $customize_fields = array(
      'descriptions' => array($description),
      'href' => $this->view->url(array('action' => $item->getTypeName(), 'gift_id' => $item->getIdentity()), 'hegift_own', true),
      'attrsA' => array('data-rel' => 'dialog'),
      'manage' => $options
    );

    return $customize_fields;
  }

  protected function getActiveRecipientId()
  {
    /**
     * @var $settings User_Model_DbTable_Settings
     */

    $viewer = Engine_Api::_()->user()->getViewer();
    $settings = Engine_Api::_()->getDbTable('settings', 'user');
    return $settings->getSetting($viewer, 'active_gift');
  }

  protected function getManageNavigation()
  {
    $navigation    = new Zend_Navigation();
    $navigation->addPages(array(
      array(
        'label'  => "HEGIFT_Received Gifts",
        'route'  => 'hegift_general',
        'action' => 'manage',
        'class' => '',
        'params' => array('action_name' => 'received'),
        'data_attrs' => ''
      ),
      array(
        'label'  => "HEGIFT_Sent Gifts",
        'route'  => 'hegift_general',
        'action' => 'manage',
        'class'  => '',
        'params' => array('action_name' => 'sent'),
        'data_attrs' => ''
      ),
    ));

    return $navigation;
  }

  protected function checkGift($gift, $sender_id = 0, $reciever_id = 0)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    if( $sender_id ) {
      $table = Engine_Api::_()->getDbTable('recipients', 'hegift');
      $select = $table->select()
        ->where('subject_id = ?', $sender_id)
        ->where('object_id = ?', $viewer->getIdentity())
        ->where('gift_id = ?', $gift->getIdentity())
      ;
      if ($table->fetchRow($select) !== null) {
        return true;
      }
    }

    if( $reciever_id ) {
      $table = Engine_Api::_()->getDbTable('recipients', 'hegift');
      $select = $table->select()
        ->where('object_id = ?', $reciever_id)
        ->where('subject_id = ?', $viewer->getIdentity())
        ->where('gift_id = ?', $gift->getIdentity())
      ;
      if ($table->fetchRow($select) !== null) {
        return true;
      }
    }

    if (!$gift || $viewer->getIdentity() != $gift->owner_id) {
      return false;
    }
    return true;
  }

  protected function getContactsForm( $text = false, $page = 1, Hegift_Model_Gift $gift )
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $balance = Engine_Api::_()->getItem('credit_balance', $viewer->getIdentity());
    $current_balance = ($balance) ? $balance->current_credit : 0;

    $table = Engine_Api::_()->getItemTable('user');
    $prefix = $table->getTablePrefix();
    $select = $table->select();

    if ( $text ) {
      $select->where($prefix . 'users.displayname LIKE ?', '%' . $text . '%');
    }

    $select
      ->setIntegrityCheck(false)
      ->from($prefix . 'users')
      ->joinLeft($prefix . 'user_membership', $prefix . 'user_membership.user_id = ' . $prefix . 'users.user_id', array())
      ->where($prefix . 'user_membership.resource_id = ?', $viewer->getIdentity())
      ->where($prefix . 'user_membership.resource_approved = 1')
      ->where($prefix . 'user_membership.user_approved = 1');

    $friends = Zend_Paginator::factory($select);
    $friends->setCurrentPageNumber($page);

    $html = '<br>';

    $form = '<form action="" id="send-gift-form" method="post">
    <textarea name="message" rows="6" cols="45"></textarea>
    <input type="radio" name="privacy" value="0" id="privacy-public" checked="checked">
    <label for="privacy-public">' . $this->view->translate('HEGIFT_Public:'). '</label>
    <input type="radio" name="privacy" value="1" id="privacy-private">
    <label for="privacy-private">' . $this->view->translate('HEGIFT_Private:'). '</label>
    <input type="hidden" name="uids" id="user-ids">
    </form>';

    $html .= $this->view->translate('Choose a friend to send a gift to:') . '<br>';
    $html .= '<form action="" id="search-contacts-form" class="filter_form"  data-theme="c">
    <input type="search" id="search-contacts" name="text" value="' . $text . '" data-theme="c">
    </form>';

    foreach($friends as $user) {
      $html .= '<input class="user_ids" type="checkbox" name="user_ids[]" value="' . $user->getIdentity() . '" id="user_' . $user->getIdentity() . '">';
      $html .= '<label for="user_' . $user->getIdentity(). '">' . $user->displayname . '</label>';
    }

    $html .= $this->view->translate('HEGIFT_You have') . '<span id="current_balance" style="color:blue;"> ' . $current_balance . ' </span>' . $this->view->translate('credits') . ', ';
    if( !$gift->owner_id && $gift->amount !== null ) {
      $html .= '<span id="gift_amount" style="color:red;">' . $gift->amount . '</span>' . $this->view->translate('HEGIFT_gifts left');
    } else {
      $html .= $this->view->translate('HEGIFT_Price') . ' <span id="gift_price" style="color:red;">' . $gift->credits . '</span> '. $this->view->translate('HEGIFT_credits');
    }
    $html .= '<button id="send-gift">'. $this->view->translate('Send') .'</button>';

    return $form . $html;
  }

  protected function setSetting(User_Model_User $user, $key, $value)
  {
    /**
     * @var $settings User_Model_DbTable_Settings
     */

    $settings = Engine_Api::_()->getDbTable('settings', 'user');
    if( null === $value ) {
      $settings->delete(array(
        'user_id = ?' => $user->getIdentity(),
        'name = ?' => $key,
      ));
    } else if( false === ($prev = $settings->getSetting($user, $key)) ) {
      $settings->insert(array(
        'user_id' => $user->getIdentity(),
        'name' => $key,
        'value' => $value,
      ));
    } else {
      $settings->update(array(
        'value' => $value,
      ), array(
        'user_id = ?' => $user->getIdentity(),
        'name = ?' => $key,
      ));
    }

    return $settings;
  }

  protected function indexCheckGift($gift, $recipient)
  {
    if (!$gift || !$recipient) {
      return false;
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($gift->getIdentity() != $recipient->gift_id || ($viewer->getIdentity() != $recipient->object_id && $viewer->getIdentity() != $recipient->subject_id)) {
      return false;
    }
    return true;
  }
}
