<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2012-09-05 12:30 ulan $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Pagecontact
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_PagecontactController extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->page_id = $page_id = $this->_getParam('page_id');
    $this->viewer = Engine_Api::_()->user()->getViewer();

    if (!$page_id) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->pageObject = $page = Engine_Api::_()->getItem('page', $page_id);

    if (!$page) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->isAllowedView = $this->getPageApi()->isAllowedView($page);

    if (!$this->isAllowedView) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

  }

  public function indexSendAction()
  {
    $form = new Pagecontact_Form_Contact($this->page_id);
    if (!$this->getRequest()->isPost()) {
      return $this->redirect($this->view->url(array('page_id', $this->pageObject->url, 'tab' => 'contact'), 'page_view'));
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return $this->redirect($this->view->url(array('page_id', $this->pageObject->url, 'tab' => 'contact'), 'page_view'));
    }

    try {
      $values = $form->getValues();

      $senderName = '';
      $senderEmail = '';

      if ($values['visitor']) {
        $senderName = $values['sender_name'];
        $senderEmail = $values['sender_email'];
      }

      $topicsTbl = Engine_Api::_()->getDbTable('topics', 'pagecontact');
      $emails = $topicsTbl->getEmails($this->page_id, $values['topic']);

      $emails = explode(',', $emails);

      foreach ($emails as $email) {
        // Make params
        $mail_settings = array(
          'date' => time(),
          'page_name' => $this->pageObject->displayname,
          'sender_name' => $senderName,
          'sender_email' => $senderEmail,
          'subject' => $values['subject'],
          'message' => $values['message'],
        );

        // send email
        Engine_Api::_()->getApi('mail', 'core')->sendSystem(
          trim($email),
          'pagecontact_template',
          $mail_settings
        );
      }
    } catch (Exception $e) {
      throw $e;
    }

    return $this->redirect($this->view->url(array('page_id' => $this->pageObject->url, 'tab' => 'contact'), 'page_view'), $this->view->translate('PAGECONTACT_Message has been sent successfully.'));
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
  }
}