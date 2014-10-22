<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:36
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_InviteController extends Apptouch_Controller_Action_Bridge
{

  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');
  }

    public function indexIndexAction()
    {
        $settings = Engine_Api::_()->getApi('settings', 'core');
        // Check if admins only
        if ($settings->getSetting('user.signup.inviteonly') == 1) {
            if (!$this->_helper->requireAdmin()->isValid()) {
                return $this->redirect($this->view->url(array('action'=>'home'), 'user_general', true));
            }
        }

        // Check for users only
        if (!$this->_helper->requireUser()->isValid()) {
          return $this->redirect($this->view->url(array('action'=>'home'), 'user_general', true));
        }

        // Make form
        $form = new Invite_Form_Invite();
        $form->setAttrib('data-rel', 'dialog');
        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))->renderContent();
            return 0;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))->renderContent();
            return 0;
        }

        // Process
        $values = $form->getValues();
        $viewer = Engine_Api::_()->user()->getViewer();
        $inviteTable = Engine_Api::_()->getDbtable('invites', 'invite');
        $db = $inviteTable->getAdapter();
        $db->beginTransaction();

        try {

            $emailsSent = $inviteTable->sendInvites($viewer, $values['recipients'], @$values['message']);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            if (APPLICATION_ENV == 'development') {
                throw $e;
            }
        }
        $html = '<br />';
        if ($emailsSent) {
            $html .= Zend_Registry::get('Zend_Translate')->_(
                    array('If the person you invited decide to join, he/she will automatically receive a friend request from you.',
                        'If the persons you invited decide to join, they will automatically receive a friend request from you.',
                        $emailsSent)
                );
        }
        $msg = '';
        if (!empty($form->invalid_emails)) {
            $msg = Zend_Registry::get('Zend_Translate')->_('Invites were not sent to these email addresses because they do not appear to be valid:');
            foreach ($form->invalid_emails as $email) {
                $msg .= '<br />' . $email;
            }
        }

        $html .= '<br />' . $msg;

        if (!empty($form->already_members)) {
            $msg = Zend_Registry::get('Zend_Translate')->_('Some of the email addresses you provided belong to existing members:');
            foreach ($form->already_members as $user) {
                $msg .= '<br />' . $user->toString();
            }
        }

        $html .= '<br />' . $msg;

        $back = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-mini' => 'true', 'data-type' => 'horizontal', 'style' => 'text-align: left'));
        $back->append($this->dom()->new_('a',
            array(
                'data-role' => 'button',
                'data-icon' => 'info',
                'data-rel' => '',
                'href' => $this->view->url(array('action'=>'home'), 'user_general', true)), $this->view->translate('OK, thanks!')));
        $html .= '' . $back;
        $emailsSent = 0;
        $this->add($this->component()->html($html))->renderContent();
    }
}
