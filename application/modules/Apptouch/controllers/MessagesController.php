<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 06.08.12
 * Time: 19:47
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_MessagesController
  extends Apptouch_Controller_Action_Bridge
{
  protected $_form;

  public function messagesInit()
  {
    $this->_helper->requireUser();
    $this->_helper->requireAuth()->setAuthParams('messages', null, 'create');
  }

  public function messagesInboxAction()
  {
    $this->addPageInfo('contentTheme', 'd');

    $viewer = Engine_Api::_()->user()->getViewer();
    $paginator = Engine_Api::_()->getItemTable('messages_conversation')
      ->getInboxPaginator($viewer);
    $paginator->setCurrentPageNumber($this->_getParam('page'));
    $unread = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);

    if(!$paginator->getTotalItemCount()) {
      $this
              ->setFormat('browse')
              /*->add($this->component()->date(array(
                  'title' => $this->view->translate(array('You have %1$s new message, %2$s total', 'You have %1$s new messages, %2$s total', $unread),
                    $this->view->locale()->toNumber($unread),
                    $this->view->locale()->toNumber($paginator->getTotalItemCount()))
                )
              )
            )*/
              ->add($this->component()->html($this->view->translate('Tip: %1$sClick here%2$s to send your first message!', "<a href='" . $this->view->url(array('action' => 'compose'), 'messages_general') . "'>", '</a>') . '<br/>'))
              ->renderContent();
      return;
    }

    $this
      ->setFormat('browse')
      ->add($this->component()->date(array(
          'title' => $this->view->translate(array('You have %1$s new message, %2$s total', 'You have %1$s new messages, %2$s total', $unread),
            $this->view->locale()->toNumber($unread),
            $this->view->locale()->toNumber($paginator->getTotalItemCount()))
        )
      )
    )
      ->add($this->component()->itemList($paginator, null, array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();
  }

  public function messagesOutboxAction()
  {
    $this->addPageInfo('contentTheme', 'd');

    $viewer = Engine_Api::_()->user()->getViewer();
    $paginator = Engine_Api::_()->getItemTable('messages_conversation')->getOutboxPaginator($viewer);
    $paginator->setCurrentPageNumber($this->_getParam('page'));

    $this
      ->setFormat('browse')
      ->setPageTitle($this->view->translate('Sent Messages'))
      ->add($this->component()->date(array(
          'title' => $this->view->translate(
            array(
              'You have %s sent message total', 'You have %s sent messages total',
              $paginator->getTotalItemCount()
            ),
            $this->view->locale()->toNumber($paginator->getTotalItemCount()
            )
          )
        )
      )
    )
      ->add($this->component()->html($this->view->translate('Tip: %1$sClick here%2$s to send your first message!', "<a href='" . $this->view->url(array('action' => 'compose'), 'messages_general') . "'>", '</a>') . '<br/>'))
      ->add($this->component()->itemList($paginator, null, array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->renderContent();


  }

  public function messagesViewAction()
  {
    $this->addPageInfo('contentTheme', 'd');
    $id = $this->_getParam('id');
    $viewer = Engine_Api::_()->user()->getViewer();

    // Get conversation info
    $conversation = Engine_Api::_()->getItem('messages_conversation', $id);

    // Make sure the user is part of the conversation
    if (!$conversation || !$conversation->hasRecipient($viewer)) {
      return $this->redirect($this->view->url(array('action' => 'inbox')));
    }

    $resource = false;

    // Check for resource
    if (!empty($conversation->resource_type) &&
      !empty($conversation->resource_id)
    ) {
      $resource = Engine_Api::_()->getItem($conversation->resource_type, $conversation->resource_id);
      if (!($resource instanceof Core_Model_Item_Abstract)) {
        return $this->redirect($this->view->url(array('action' => 'inbox')));
      }
      //      $this->view->resource = $resource;
    }
    // Otherwise get recipients
    else {
      $recipients = $conversation->getRecipients();

      $blocked = false;
      $blocker = "";

      // This is to check if the viewered blocked a member
      $viewer_blocked = false;
      $viewer_blocker = "";

      foreach ($recipients as $recipient) {
        if ($viewer->isBlockedBy($recipient)) {
          $blocked = true;
          $blocker = $recipient;
        }
        elseif ($recipient->isBlockedBy($viewer)) {
          $viewer_blocked = true;
          $viewer_blocker = $recipient;
        }
      }
      //      $this->view->blocked = $blocked;
      //      $this->view->blocker = $blocker;
      //      $this->view->viewer_blocked = $viewer_blocked;
      //      $this->view->viewer_blocker = $viewer_blocker;
    }


    // Can we reply?
    $locked = $conversation->locked;
    if (!$conversation->locked) {

      // Assign the composing junk
      $composePartials = array();
      foreach (Zend_Registry::get('Engine_Manifest') as $data)
      {
        if (empty($data['composer'])) continue;
        foreach ($data['composer'] as $type => $config)
        {
          $composePartials[] = $config['script'];
        }
      }
      //      $this->view->composePartials = $composePartials;


      // Process form
      $form = new Messages_Form_Reply();
      if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
        $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
        $db->beginTransaction();
        try
        {
          // Try attachment getting stuff
          $attachment = null;
          $attachmentData = $this->getRequest()->getParam('attachment');
          if (!empty($attachmentData) && !empty($attachmentData['type'])) {
            $type = $attachmentData['type'];
            $config = null;
            foreach (Zend_Registry::get('Engine_Manifest') as $data)
            {
              if (!empty($data['composer'][$type])) {
                $config = $data['composer'][$type];
              }
            }
            if ($config) {
              $plugin = Engine_Api::_()->loadClass($config['plugin']);
              $method = 'onAttach' . ucfirst($type);
              $attachment = $plugin->$method($attachmentData);

              $parent = $attachment->getParent();
              if ($parent->getType() === 'user') {
                $attachment->search = 0;
                $attachment->save();
              }
              else {
                $parent->search = 0;
                $parent->save();
              }

            }
          }

          $values = $form->getValues();
          $values['conversation'] = (int)$id;

          $conversation->reply(
            $viewer,
            $values['body'],
            $attachment
          );
          /*
         Engine_Api::_()->messages()->replyMessage(
           $viewer,
           $values['conversation'],
           $values['body'],
           $attachment
         );
          *
          */

          // Send notifications
          foreach ($recipients as $user)
          {
            if ($user->getIdentity() == $viewer->getIdentity()) {
              continue;
            }
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
              $user,
              $viewer,
              $conversation,
              'message_new'
            );
          }

          // Increment messages counter
          Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
          if( Engine_Api::_()->apptouch()->isApp() ) {
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('ios.messages.creations');
          } else {
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('apptouch.messages.creations');
          }


          $db->commit();
        }
        catch (Exception $e)
        {
          $db->rollBack();
          throw $e;
        }

        $form->populate(array('body' => ''));
        return $this->redirect($this->view->url(array('action' => 'view', 'id' => $id)));
      } else {
        $this->add($this->component()->form($form), 10);
      }
    }

    // Make sure to load the messages after posting :P
    $messages = $conversation->getMessages($viewer);
    $conversation->setAsRead($viewer);

    $this->setPageTitle($this->view->translate('My Messages'))
      ->add($this->component()->navigation('main'));
    $h3 = $this->dom()->new_('h3');
    if ('' != ($title = trim($conversation->getTitle()))) {
      $h3->text = $title;
    }
    else
      $h3->text = $this->view->translate('(No Subject)');
    //    $this->printArr($h3.'');
    $this->add($this->component()->html($h3));
    if ($resource) {
      $converDesc = $this->view->translate('To members of %1$s', $resource->toString());
    }
    // Recipients
    else {
      $you = array_shift($recipients);
      $you = $this->view->htmlLink($you->getHref(), ($this->view->viewer()->isSelf($you) ? $this->view->translate('You') : $you->getTitle()));
      $them = array();
      foreach ($recipients as $r) {
        if ($r != $this->view->viewer()) {
          $them[] = ($r == $blocker ? "<s>" : "") . $this->view->htmlLink($r->getHref(), $r->getTitle()) . ($r == $blocker ? "</s>" : "");
        } else {
          $them[] = $this->view->htmlLink($r->getHref(), $this->view->translate('You'));
        }
      }

      if (count($them)) $converDesc = $this->view->translate('Between %1$s and %2$s', $you, $this->view->fluentList($them));
      else $converDesc = $this->view->translate('Conversation with a deleted member.');
    }
    $this->add($this->component()->html($converDesc))
      ->add($this->component()->html($this->view->htmlLink(array(
      'action' => 'delete',
      'id' => null,
      'place' => 'view',
      'message_ids' => $conversation->conversation_id,
    ), $this->view->translate('Delete'), array(
      'data-role' => 'button',
      'data-rel' => 'dialog',
    ))));
    $ul = $this->dom()->new_('ul', array('data-role' => 'listview'));
    foreach ($messages as $message){
      $user = $this->view->user($message->user_id);
      $li = $this->dom()->new_('li', array(), $this->view->itemPhoto($user, 'thumb.normal'), array(
        $this->dom()->new_('h3', array(), $this->view->htmlLink($user->getHref(), $user->getTitle())),
        $this->dom()->new_('span', array('class' => 'ui-li-aside'), $this->view->timestamp($message->date)),
        $this->dom()->new_('p', array(), nl2br(html_entity_decode($message->body))),
      ));
      if (!empty($message->attachment_type) && null !== ($attachment = $this->view->item($message->attachment_type, $message->attachment_id))){
        if (null != ($richContent = $attachment->getRichContent(false, array('message' => $message->conversation_id)))) {
          $li->append($this->dom()->new_('p', array(), $richContent));
        } else {
          if (null !== $attachment->getPhotoUrl()) {
            $li->append($this->dom()->new_('p', array(), $this->view->itemPhoto($attachment, 'thumb.normal')));
          }
          $li->append($this->dom()->new_('h3', array(), $this->view->htmlLink($attachment->getHref(array('message' => $message->conversation_id)), $attachment->getTitle())));
          $li->append($this->dom()->new_('p', array(), $attachment->getDescription()));
        }
      }
      $ul->append($li);
    }
    $this->add($this->component()->html('<br />' . $ul . '<br />'));
    $this->renderContent();
  }

  public function messagesComposeAction()
  {
    $this->addPageInfo('contentTheme', 'd');

    // Make form
    $form = new Messages_Form_Compose();
    $form->toValues->setIsArray(true);
    // Get params
    $multi = $this->_getParam('multi');
    $to = $this->_getParam('to');
    $viewer = Engine_Api::_()->user()->getViewer();
    $toObject = null;

    // Build
    $isPopulated = false;
    if (!empty($to) && (empty($multi) || $multi == 'user')) {
      $multi = null;
      // Prepopulate user
      $toUser = Engine_Api::_()->getItem('user', $to);
      if ($toUser instanceof User_Model_User &&
        (!$viewer->isBlockedBy($toUser) && !$toUser->isBlockedBy($viewer)) &&
        isset($toUser->user_id)
      ) {
        $toObject = $toUser;
        $form->toValues->setValue(array($toUser->getGuid()));
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    } else if (!empty($to) && !empty($multi)) {
      // Prepopulate group/event/etc
      $item = Engine_Api::_()->getItem($multi, $to);
      // Potential point of failure if primary key column is something other
      // than $multi . '_id'
      $item_id = $multi . '_id';
      if ($item instanceof Core_Model_Item_Abstract &&
        isset($item->$item_id) && (
      $item->membership()->isMember($viewer)
/*            $item->isOwner($viewer) ||
            $item->authorization()->isAllowed($viewer, 'edit')

 */
      )
      ) {
        $toObject = $item;
        $form->toValues->setValue(array($item->getGuid()));
        $isPopulated = true;
      } else {
        $multi = null;
        $to = null;
      }
    }

    // Assign the composing stuff
    $composePartials = array();
    foreach (Zend_Registry::get('Engine_Manifest') as $data)
    {
      if (empty($data['composer'])) continue;
      foreach ($data['composer'] as $type => $config)
      {
        $composePartials[] = $config['script'];
      }
    }

    // Get config
    $maxRecipients = 10;

    $settings = array(
      'suggestUrl' => $this->view->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'format' => 'json'), 'default', true),
      'maxRecipients' => 10
    );
    if (!empty($isPopulated) && !empty($toObject)) {
      $settings['to'] = array(
        'id' => sprintf("%d", $toObject->getIdentity()),
        'type' => $toObject->getType(),
        'guid' => $toObject->getGuid(),
        'url' => $toObject->getHref(),
        'label' => $this->view->string()->escapeJavascript($toObject->getTitle()),
        'photo' => $this->view->itemPhoto($toObject, 'thumb.icon')
      );

    }
    $this->addPageInfo('messages', $settings);
    $this
      ->setPageTitle($this->view->translate('Compose Message'))
      ->add($this->component()->navigation('main'));

    // Check method/data
    if (!$this->getRequest()->isPost()) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    // Process
    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();

    try {
      // Try attachment getting stuff
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');
      if (!empty($attachmentData) && !empty($attachmentData['type'])) {
        $type = $attachmentData['type'];
        $config = null;
        foreach (Zend_Registry::get('Engine_Manifest') as $data)
        {
          if (!empty($data['composer'][$type])) {
            $config = $data['composer'][$type];
          }
        }
        if ($config) {
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach' . ucfirst($type);
          $attachment = $plugin->$method($attachmentData);
          $parent = $attachment->getParent();
          if ($parent->getType() === 'user') {
            $attachment->search = 0;
            $attachment->save();
          }
          else {
            $parent->search = 0;
            $parent->save();
          }
        }
      }

      $viewer = Engine_Api::_()->user()->getViewer();
      $values = $form->getValues();

      // Prepopulated
      if ($toObject instanceof User_Model_User) {
        $recipientsUsers = array($toObject);
        $recipients = $toObject;
      } else if ($toObject instanceof Core_Model_Item_Abstract &&
        method_exists($toObject, 'membership')
      ) {
        $recipientsUsers = $toObject->membership()->getMembers();
        //        $recipients = array();
        //        foreach( $recipientsUsers as $recipientsUser ) {
        //          $recipients[] = $recipientsUser->getIdentity();
        //        }
        $recipients = $toObject;
      }
      // Normal
      else {
        $recipients = $values['toValues'];
        // clean the recipients for repeating ids
        // this can happen if recipient is selected and then a friend list is selected
        $recipients = array_unique($recipients);
        // Slice down to 10
        $recipients = array_slice($recipients, 0, $maxRecipients);
        $recipientsUsers = Engine_Api::_()->getItemMulti('user', $recipients);
      }

      // Create conversation
      $conversation = Engine_Api::_()->getItemTable('messages_conversation')->send(
        $viewer,
        $recipients,
        $values['title'],
        $values['body'],
        $attachment
      );

      // Send notifications
      foreach ($recipientsUsers as $user) {
        if ($user->getIdentity() == $viewer->getIdentity()) {
          continue;
        }
        Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
          $user,
          $viewer,
          $conversation,
          'message_new'
        );
      }

      // Increment messages counter
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('messages.creations');
      if( Engine_Api::_()->apptouch()->isApp() ) {
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('ios.messages.creations');
      } else {
        Engine_Api::_()->getDbtable('statistics', 'core')->increment('apptouch.messages.creations');
      }


      // Commit
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    return $this->redirect($conversation, Zend_Registry::get('Zend_Translate')->_('Your message has been sent successfully.'));
  }

  public function messagesSuccessAction()
  {

  }

  public function messagesDeleteAction()
  {
    if (!$this->_helper->requireUser()->isValid()) return;

    $message_ids = $this->getRequest()->getParam('message_ids');
    $messages = explode(',', $message_ids);

    $place = $this->getRequest()->getParam('place');
    $form = $this->dom()->new_('form', array('action' => $this->view->url(), 'method' => 'POST'), '', array(
      $this->dom()->new_('h3', array(), $this->view->translate('Delete Message(s)?')),
      $this->dom()->new_('p', array(), $this->view->translate('Are you sure that you want to delete the selected message(s)? This action cannot be undone.')),
      $this->dom()->new_('input', array('type' => 'hidden', 'value' => $message_ids)),
      $this->dom()->new_('input', array('type' => 'hidden', 'value' => $place)),
      $this->dom()->new_('button', array('type' => 'submit', 'value' => $place), $this->view->translate('Delete')),
      $this->dom()->new_('a', array('data-rel' => 'back', 'data-role' => 'button'), $this->view->translate('Cancel')),
    ));
    if (!$this->getRequest()->isPost()){
      $this->add($this->component()->html($form))
        ->renderContent();
      return;
    }

    $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $deleted_conversation_ids = array();

    $db = Engine_Api::_()->getDbtable('messages', 'messages')->getAdapter();
    $db->beginTransaction();
    try {
      foreach ($messages as $message_id) {
        $recipients = Engine_Api::_()->getItem('messages_conversation', $message_id)->getRecipientsInfo();
        //$recipients = Engine_Api::_()->getApi('core', 'messages')->getConversationRecipientsInfo($message_id);
        foreach ($recipients as $r) {
          if ($viewer_id == $r->user_id) {
            $deleted_conversation_ids[] = $r->conversation_id;
            $r->inbox_deleted = true;
            $r->outbox_deleted = true;
            $r->save();
          }
        }
      }
      $db->commit();
    } catch (Exception $e) {
      $db->rollback();
      throw $e;
    }

    $message = Zend_Registry::get('Zend_Translate')->_('The selected messages have been deleted.');

    if ($place != 'view') {
      return $this->redirect('refresh', $message, true);
    }
    else {
      return $this->redirect(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'inbox')), $message, true);
    }
  }

}
