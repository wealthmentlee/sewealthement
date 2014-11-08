<?php

/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 13.06.12
 * Time: 12:44
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_CoreController
    extends Apptouch_Controller_Action_Bridge
{
    public function indexIndexAction()
    {
        if (Engine_Api::_()->user()->getViewer()->getIdentity()) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'home'), 'user_general', true);
        }

      // check public settings

      return $this->redirect($this->view->url(array(), 'user_login', true));
    }

    public function boardIndexAction()
    {
        $this->attrPage('class', 'dashboard-page');
        $searchForm = $this->getSearchForm();
        $viewer = Engine_Api::_()->user()->getViewer();
        $searchForm->setAction($this->view->url(array('module' => 'core', 'controller' => 'search', 'action' => 'index'), 'default', true));

        $nCount = Engine_Api::_()->getDbTable('notifications', 'activity')->hasNotifications($viewer);
        $rCount = Engine_Api::_()->getDbtable('notifications', 'activity')->getRequestsPaginator($viewer)->getTotalItemCount();
        $mCount = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);

        $notificationsBtn = $this->dom()->new_('a', array(
            'data-role' => 'button',
            'data-icon' => 'bell',
            'data-theme' => 'b',
            'data-inline' => true,
            'data-transition' => 'slide',
            'data-iconpos' => 'left',
            'href' => $this->view->url(array('module' => 'activity', 'controller' => 'notifications'), 'default', true),
            'class' => 'notifications-btn'
        ), $nCount);
//        $requestsBtn = $this->dom()->new_('a', array(
//            'data-role' => 'button',
//            'data-icon' => 'person',
//            'data-theme' => $rCount ? 'e' : 'a',
//            'data-inline' => true,
//            'data-transition' => 'slide',
//            'data-iconpos' => 'left',
//            'href' => $this->view->url(array('module' => 'activity', 'controller' => 'notifications', 'show' => '1'), 'default', true),
//            'class' => 'requests-btn'
//        ), $rCount);

        $messagesBtn = $this->dom()->new_('a', array(
            'data-role' => 'button',
            'data-icon' => 'speech',
            'data-theme' => 'b',
            'data-inline' => true,
            'data-transition' => 'slide',
            'data-iconpos' => 'left',
            'href' => $this->view->url(array('action' => 'inbox'), 'messages_general', true),
            'class' => 'messages-btn'
        ), $mCount);

        $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
        $isStoreEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store');
        $cartBtn = '';

        $dashboardHeaderMenus = array(
          'updates' => '0',
          'requests' => '0',
          'messages' => '0',
          'store' => '0'
        );

        if ($isPageEnabled && $isStoreEnabled) {
            $viewer = Engine_Api::_()->user()->getViewer();
            $cart_table = Engine_Api::_()->getItemTable('store_cart');
//          $order_table = Engine_Api::_()->getItemTable('store_order');
            $cart = $cart_table->getCart($viewer->getIdentity());
            if ($cart->hasItem()) {
//                $dashboardHeaderMenus['store'] = '1';
                $items = $cart->getItems()->toArray();
                $cartBtn = $this->dom()->new_('a', array(
                    'data-role' => 'button',
                    'data-icon' => 'shopping-cart',
                    'data-theme' => 'b',
                    'data-inline' => true,
                    'data-transition' => 'slide',
                    'data-iconpos' => 'left',
                    'href' => $this->view->url(array('module' => 'store', 'controller' => 'cart', 'action' => 'index'), 'default', true),
                    'class' => 'store-btn'
                ), count($items));
                // . ', Total Price - ' . $this->view->locale()->toCurrency($total, $this->_order->currency));
            }
        }
        $isStoreEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store');
        $token = ($isStoreEnabled) ? Engine_Api::_()->store()->getToken() : false;

        if ($token) {
            $buttons = array(
                $cartBtn
            );
        } else {
            $buttons = array(
                $notificationsBtn,
//                $requestsBtn,
                $messagesBtn,
                $cartBtn
            );
        }

        $controlgroup = $this->dom()->new_('div', array(
            'data-role' => 'controlgroup',
            'data-mini' => true,
            'data-type' => 'horizontal',
            'class' => 'notifier-group'
        ), '', $buttons);
        $this->renderLangSwitcher();

      if( $viewer->getIdentity() ) {
        $dashboardHeaderMenus['updates'] = '1';
        $dashboardHeaderMenus['requests'] = '1';
        $dashboardHeaderMenus['messages'] = '1';
      } else {

      }
      if ($viewer->getIdentity() || $token) {
          $place = 'header';
          $this->add($this->component()->html($controlgroup), null, $place);

        $this->addPageInfo('dashboardHeaderMenus', $dashboardHeaderMenus);
        $this->addPageInfo('dashboardHeaderBadges', array('updates' => $nCount, 'requests' => $rCount, 'messages' => $mCount, 'store' => $items ? count($items) : 0));
      }

      $this
          ->add($this->component()->itemSearch($searchForm), null, 'content')
          ->add($this->component()->dashboard(), null, 'content')
          ->renderContent();
    }

    public function confirmConfirmAction()
    {
        $params = $this->getRequest()->getParams();
        $this->view->confirm_route = $params['confirm_route'];
        $this->view->deny_route = $params['deny_route'];
        $this->view->args = $params;
        $this->view->base_url = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->view->confirm_text = $params['confirm_text'];
    }

//  Comment Controller {

    public function commentInit()
    {
        $type = $this->_getParam('type');
        $identity = $this->_getParam('id');
        if ($type && $identity) {
            $item = Engine_Api::_()->getItem($type, $identity);
            if ($item instanceof Core_Model_Item_Abstract &&
                (method_exists($item, 'comments') || method_exists($item, 'likes'))
            ) {
                if (!Engine_Api::_()->core()->hasSubject()) {
                    Engine_Api::_()->core()->setSubject($item);
                }
            }
        }

        $this->_helper->requireSubject();
    }

    public function commentListAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        // Perms
        $this->view->canComment = $canComment = $subject->authorization()->isAllowed($viewer, 'comment');
        $this->view->canDelete = $subject->authorization()->isAllowed($viewer, 'edit');

        // Likes
        $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
        $this->view->likes = $likes = $subject->likes()->getLikePaginator();

        // Comments

        // If has a page, display oldest to newest
        $commentSelect = $subject->comments()->getCommentSelect();
        $commentSelect->order('comment_id DESC');
        $comments = Zend_Paginator::factory($commentSelect);
        $comments->setCurrentPageNumber(1);
        $comments->setItemCountPerPage($comments->getTotalItemCount());
        $this->view->comments = $comments;

        if ($viewer->getIdentity() && $canComment) {
            $this->view->form = $form = new Core_Form_Comment_Create();
            $form->populate(array(
                'identity' => $subject->getIdentity(),
                'type' => $subject->getType(),
            ));
        }
    }

    public function commentLikeAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $comment_id = $this->_getParam('comment_id');

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();

        try {

            $commentedItem->likes()->addLike($viewer);

            // Add notification
            $owner = $commentedItem->getOwner();
            $owner_guid = $owner->getGuid();
            if ($owner->getType() == 'user' && $owner->getIdentity() != $viewer->getIdentity()) {
                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                $notifyApi->addNotification($owner, $viewer, $commentedItem, 'liked', array(
                    'label' => $commentedItem->getShortType()
                ));
            }

            // Stats
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');
            if (Engine_Api::_()->apptouch()->isApp()) {
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('ios.core.likes');
            } else {
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('apptouch.core.likes');
            }


            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->status = true;
        $this->view->like = true;
        //    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like added');
        //    $this->view->body = $this->view->action('list', 'comment', 'core', array(
        //      'type' => $type,
        //      'id' => $id,
        //      'format' => 'html',
        //      'page' => 1,
        //    ));
        //    $this->_helper->contextSwitch->initContext();
    }

    public function commentUnlikeAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();
        $comment_id = $this->_getParam('comment_id');

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        if ($comment_id) {
            $commentedItem = $subject->comments()->getComment($comment_id);
        } else {
            $commentedItem = $subject;
        }

        // Process
        $db = $commentedItem->likes()->getAdapter();
        $db->beginTransaction();

        try {
            $commentedItem->likes()->removeLike($viewer);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        // For comments, render the resource
        if ($subject->getType() == 'core_comment') {
            $type = $subject->resource_type;
            $id = $subject->resource_id;
            Engine_Api::_()->core()->clearSubject();
        } else {
            $type = $subject->getType();
            $id = $subject->getIdentity();
        }

        $this->view->status = true;
        $this->view->like = false;

        //    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Like removed');
        //    $this->view->body = $this->view->action('list', 'comment', 'core', array(
        //      'type' => $type,
        //      'id' => $id,
        //      'format' => 'html',
        //      'page' => 1,
        //    ));
        //    $this->_helper->contextSwitch->initContext();
    }

    public function commentDeleteAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        // Comment id
        $comment_id = $this->_getParam('comment_id');
        if (!$comment_id) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment');
            return;
        }

        // Comment
        $comment = $subject->comments()->getComment($comment_id);
        if (!$comment) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('No comment or wrong parent');
            return;
        }

        // Authorization
        if (!$subject->authorization()->isAllowed($viewer, 'edit') &&
            ($comment->poster_type != $viewer->getType() ||
                $comment->poster_id != $viewer->getIdentity())
        ) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not allowed');
            return;
        }

        // Method
        if (!$this->getRequest()->isPost()) {
            $this->view->status = false;
//      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
            return;
        }

        // Process
        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        try {
            $subject->comments()->removeComment($comment_id);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->deleted = true;
        $this->view->comment_count = $subject->comments()->getCommentCount();
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment deleted');
    }

    public function commentCreateAction()
    {
        if (!$this->_helper->requireUser()->isValid()) {
            return;
        }
        if (!$this->_helper->requireAuth()->setAuthParams(null, null, 'comment')->isValid()) {
            return;
        }

        $viewer = Engine_Api::_()->user()->getViewer();
        $subject = Engine_Api::_()->core()->getSubject();

        $form = new Core_Form_Comment_Create();

        if (!$this->getRequest()->isPost()) {
            $this->view->status = false; //      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");
            ;
            return;
        }

        if (!$form->isValid($this->_getAllParams())) {
            $this->view->status = false;
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
            return;
        }

        // Process

        // Filter HTML
        $filter = new Zend_Filter();
        $filter->addFilter(new Engine_Filter_Censor());
        $filter->addFilter(new Engine_Filter_HtmlSpecialChars());

        $body = $form->getValue('body');
        $body = $filter->filter($body);


        $db = $subject->comments()->getCommentTable()->getAdapter();
        $db->beginTransaction();

        $comment_format = array();
        try {
            $canDelete = $subject->authorization()->isAllowed($viewer, 'edit');
            $canComment = $subject->authorization()->isAllowed($viewer, 'comment');

            $comment = $subject->comments()->addComment($viewer, $body);
            $comment_format['id'] = $comment->getIdentity();
            $comment_format['poster'] = $this->subject($comment->getPoster());


            /**
             * Wall Modification {
             */
            $isWall = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('wall');
            if ($isWall) {
                $page = null;
                $poster = Engine_Api::_()->getItem($comment->poster_type, $comment->poster_id);

                if (Engine_Api::_()->wall()->isOwnerTeamMember($subject, $poster)) {
                    $page = Engine_Api::_()->wall()->getSubjectPage($subject);
                }

                if ($page && $page->getType() == 'page' && Engine_Api::_()->wall()->isOwnerTeamMember($page, $comment->getPoster())) {
                    $comment_format['poster'] = $this->subject($page);
                }
            }

            /**
             * } Wall Modification
             */


            $comment_format['body'] = $comment->body;
            $comment_format['creation_date'] = $this->view->timestamp($comment->creation_date);
            $comment_format['options'] = array();
            if ($canComment)
                $comment_format['options']['like'] = $comment->likes()->isLike($this->view->viewer());

            $comment_format['options']['delete'] = $canDelete;

            $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
            $subjectOwner = $subject->getOwner('user');

            // Activity
            $action = $activityApi->addActivity($viewer, $subject, 'comment_' . $subject->getType(), '', array('is_mobile' => true,
                'owner' => $subjectOwner->getGuid(),
                'body' => $body
            ));

            //$activityApi->attachActivity($action, $subject);

            // Notifications

            // Add notification for owner (if user and not viewer)
            $this->view->subject = $subject->getGuid();
            $this->view->owner = $subjectOwner->getGuid();
            if ($subjectOwner->getType() == 'user' && $subjectOwner->getIdentity() != $viewer->getIdentity()) {
                $notifyApi->addNotification($subjectOwner, $viewer, $subject, 'commented', array(
                    'label' => $subject->getShortType()
                ));
            }

            // Add a notification for all users that commented or like except the viewer and poster
            // @todo we should probably limit this
            $commentedUserNotifications = array();
            foreach ($subject->comments()->getAllCommentsUsers() as $notifyUser) {
                if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity()) continue;

                // Don't send a notification if the user both commented and liked this
                $commentedUserNotifications[] = $notifyUser->getIdentity();

                $notifyApi->addNotification($notifyUser, $viewer, $subject, 'commented_commented', array(
                    'label' => $subject->getShortType()
                ));
            }

            // Add a notification for all users that liked
            // @todo we should probably limit this
            foreach ($subject->likes()->getAllLikesUsers() as $notifyUser) {
                // Skip viewer and owner
                if ($notifyUser->getIdentity() == $viewer->getIdentity() || $notifyUser->getIdentity() == $subjectOwner->getIdentity()) continue;

                // Don't send a notification if the user both commented and liked this
                if (in_array($notifyUser->getIdentity(), $commentedUserNotifications)) continue;

                $notifyApi->addNotification($notifyUser, $viewer, $subject, 'liked_commented', array(
                    'label' => $subject->getShortType()
                ));
            }

            // Increment comment count
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');
            if (Engine_Api::_()->apptouch()->isApp()) {
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('ios.core.comments');
            } else {
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('apptouch.core.comments');
            }


            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->view->status = true;
        $this->view->message = 'Comment added';
        $this->view->comment_count = $subject->comments()->getCommentCount();
        $this->view->comment = $comment_format;
    }


//  } Comment Controller

// Error Controller {
    public function errorInit()
    {
        $this->setFormat('error');
    }

    public function errorErrorAction()
    {
        $error = $this->_getParam('error_handler');
        $this->view->error_code = $error_code = Engine_Api::getErrorCode(true);

        // Handle missing pages
        switch ($error->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                return $this->_forward('notfound');
                break;

            default:
                break;
        }

        // Log this message
        if (isset($error->exception) &&
            Zend_Registry::isRegistered('Zend_Log') &&
            ($log = Zend_Registry::get('Zend_Log')) instanceof Zend_Log
        ) {
            // Only log if in production or the exception is not an instance of Engine_Exception
            $e = $error->exception;
            if ('production' === APPLICATION_ENV || !($e instanceof Engine_Exception)) {
                $output = '';
                $output .= PHP_EOL . 'Error Code: ' . $error_code . PHP_EOL;
                $output .= $e->__toString();
                $log->log($output, Zend_Log::CRIT);
            }
        }

        //$this->getResponse()->setRawHeader('HTTP/1.1 500 Internal server error');
        $this->view->status = false;
        $this->view->errorName = get_class($error->exception);

        if (APPLICATION_ENV != 'production') {
            if ($error->exception instanceof Exception) {
                $this->view->error = $error->exception->__toString();
            }
        } else {
            $this->view->message = Zend_Registry::get('Zend_Translate')->_('An error has occurred');
        }
    }

    public function errorNotfoundAction()
    {
        // 404 error -- controller or action not found
        $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('The requested resource could not be found.');
    }

    public function errorRequiresubjectAction()
    {
        return $this->_forward('notfound');

        // 404 error -- subject not found
        $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('The requested resource could not be found.');
    }

    public function errorRequireauthAction()
    {
        // 403 error -- authorization failed
        if (!$this->_helper->requireUser()->isValid()) return;
        $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('You are not authorized to access this resource.');
    }

    public function errorRequireuserAction()
    {
        // 403 error -- authorization failed
        $this->getResponse()->setRawHeader($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
        $this->view->status = false;
        $this->view->error = Zend_Registry::get('Zend_Translate')->_('You are not authorized to access this resource.');
        // Show the login form for them :P
        $this->view->form = $form = new Apptouch_Form_Login();
        $form->addError('Please sign in to continue..');
        $form->return_url->setValue(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
        $this->add($this->component()->form($form))
            ->renderContent();


        // Render
//    $this->_helper->content
//    //->setNoRender()
//      ->setEnabled();
    }

    public function errorRequireadminAction()
    {
        // Should probably make this do something else later
        //$this->_helper->layout->setLayout('admin');
        return $this->_forward('notfound');
    }

// } Error Controller

// Help Controller {
    public function helpInit()
    {
        $this->addPageInfo('contentTheme', 'd');
    }

    public function helpContactAction()
    {
        $translate = Zend_Registry::get('Zend_Translate');
        $form = new Core_Form_Contact();
        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        // Success! Process
        // Mail gets logged into database, so perform try/catch in this Controller
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try {
            // the contact form is emailed to the first SuperAdmin by default
            $users_table = Engine_Api::_()->getDbtable('users', 'user');
            $users_select = $users_table->select()
                ->where('level_id = ?', 1)
                ->where('enabled >= ?', 1);
            $super_admin = $users_table->fetchRow($users_select);
            $adminEmail = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.mail.contact');
            if (!$adminEmail) {
                $adminEmail = $super_admin->email;
            }

            $viewer = Engine_Api::_()->user()->getViewer();

            $values = $form->getValues();

            // Check for error report
            $error_report = '';
            $name = $this->_getParam('name');
            $loc = $this->_getParam('loc');
            $time = $this->_getParam('time');
            if ($name && $loc && $time) {
                $error_report .= "\r\n";
                $error_report .= "\r\n";
                $error_report .= "-------------------------------------";
                $error_report .= "\r\n";
                $error_report .= $this->view->translate('The following information about an error was included with this message:');
                $error_report .= "\r\n";
                $error_report .= $this->view->translate('Exception: ') . base64_decode(urldecode($name));
                $error_report .= "\r\n";
                $error_report .= $this->view->translate('Location: ') . base64_decode(urldecode($loc));
                $error_report .= "\r\n";
                $error_report .= $this->view->translate('Time: ') . date('c', base64_decode(urldecode($time)));
                $error_report .= "\r\n";
            }

            // Make params
            $mail_settings = array(
                'host' => $_SERVER['HTTP_HOST'],
                'email' => $adminEmail,
                'date' => time(),
                'recipient_title' => $super_admin->getTitle(),
                'recipient_link' => $super_admin->getHref(),
                'recipient_photo' => $super_admin->getPhotoUrl('thumb.icon'),
                'sender_title' => $values['name'],
                'sender_email' => $values['email'],
                'message' => $values['body'],
                'error_report' => $error_report,
            );

            if ($viewer && $viewer->getIdentity()) {
                $mail_settings['sender_title'] .= ' (' . $viewer->getTitle() . ')';
                $mail_settings['sender_email'] .= ' (' . $viewer->email . ')';
                $mail_settings['sender_link'] = $viewer->getHref();
            }

            // send email
            Engine_Api::_()->getApi('mail', 'core')->sendSystem(
                $adminEmail,
                'core_contact',
                $mail_settings
            );

            // if the above did not throw an exception, it succeeded
            $db->commit();
            $this->view->status = true;
            $this->view->message = $translate->_('Thank you for contacting us!');

        } catch (Zend_Mail_Transport_Exception $e) {
            $db->rollBack();
            throw $e;
        }
        if ($this->view->status)
            $form->addNotice($this->view->message);
        $this
            ->add($this->component()->form($form))
            ->renderContent();
    }

    public function helpTermsAction()
    {
        $str = $this->view->translate('_CORE_TERMS_OF_SERVICE');
        if ($str == strip_tags($str)) {
            // there is no HTML tags in the text
            $str = nl2br($str);
        }

        $this->add($this->component()->html($str))
            ->renderContent();
    }

    public function helpPrivacyAction()
    {
        // to change, edit language variable "_CORE_PRIVACY_STATEMENT"
        $str = $this->view->translate('_CORE_PRIVACY_STATEMENT');
        if ($str == strip_tags($str)) {
            // there is no HTML tags in the text
            $str = nl2br($str);
        }

        $this->addPageInfo('contentTheme', 'd');

        $this->add($this->component()->html('<h2>' . $this->view->translate('Privacy Statement') . '</h2>'))
            ->add($this->component()->html($str))
            ->renderContent();

    }

// } Help Controller


// Link Controller {

    public function linkIndexAction()
    {
        $key = $this->_getParam('key');
        $uri = $this->_getParam('uri');
        $link = Engine_Api::_()->getItem('core_link', $this->_getParam('id', $this->_getParam('link_id')));
        Engine_Api::_()->core()->setSubject($link);

        if (!$this->_helper->requireSubject()->isValid()) return;
        //if( !$this->_helper->requireAuth()->setAuthParams($link, null, 'view')->isValid() ) return;

        if ($key != $link->getKey()) {
            throw new Exception('whoops');
        }

        $link->view_count++;
        $link->save();

//    $this->_helper->viewRenderer->setNoRender(true);
//    $this->_helper->redirector->gotoUrl($link->uri);
        $this->redirect($link->uri);
    }

    public function linkPreviewAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;
        if (!$this->_helper->requireAuth()->setAuthParams('core_link', null, 'create')->isValid()) return;

        // clean URL for html code
        $uri = trim(strip_tags($this->_getParam('uri')));
        //$uri = $this->_getParam('uri');
        $info = parse_url($uri);
        $this->view->url = $uri;

        try {
            $client = new Zend_Http_Client($uri, array(
                'maxredirects' => 2,
                'timeout' => 10,
            ));

            // Try to mimic the requesting user's UA
            $client->setHeaders(array(
                'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
                'X-Powered-By' => 'Zend Framework'
            ));

            $response = $client->request();

            // Get content-type
            list($contentType) = explode(';', $response->getHeader('content-type'));
            $this->view->contentType = $contentType;

            // Prepare
            $this->view->title = null;
            $this->view->description = null;
            $this->view->thumb = null;
            $this->view->imageCount = 0;
            $this->view->images = array();

            // Handling based on content-type
            switch (strtolower($contentType)) {

                // Images
                case 'image/gif':
                case 'image/jpeg':
                case 'image/jpg':
                case 'image/tif': // Might not work
                case 'image/xbm':
                case 'image/xpm':
                case 'image/png':
                case 'image/bmp': // Might not work
                    $this->_previewImage($uri, $response);
                    break;

                // HTML
                case '':
                case 'text/html':
                    $this->_previewHtml($uri, $response);
                    break;

                // Plain text
                case 'text/plain':
                    $this->_previewText($uri, $response);
                    break;

                // Unknown
                default:
                    break;
            }
        } catch (Exception $e) {
            $this->view->error = $e . '';
            throw $e;
            //$this->view->title = $uri;
            //$this->view->description = $uri;
            //$this->view->images = array();
            //$this->view->imageCount = 0;
        }
    }

    public function linkDeleteAction()
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $link = Engine_Api::_()->getItem('core_link', $this->getRequest()->getParam('link_id'));

        if (!$this->_helper->requireAuth()->setAuthParams($link, null, 'delete')->isValid()) return;

        $form = new Core_Form_Link_Delete();
        $translate = Zend_Registry::get('Zend_Translate');

        if (!$link) {
            $this->view->error = Zend_Registry::get('Zend_Translate')->_("Link doesn't exists or not authorized to delete");
            return;
        }

        if (!$this->getRequest()->isPost()) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->add($this->component()->form($form))
                ->renderContent();
            return;
        }

        $db = $link->getTable()->getAdapter();
        $db->beginTransaction();

        try {
            $link->delete();
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

//    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Link has been deleted.');

        return $this->redirect('refresh', Zend_Registry::get('Zend_Translate')->_('Link has been deleted.'));
    }

    protected function _previewImage($uri, Zend_Http_Response $response)
    {
        $this->view->imageCount = 1;
        $this->view->images = array($uri);
    }

    protected function _previewText($uri, Zend_Http_Response $response)
    {
        $body = $response->getBody();
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
            preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)
        ) {
            $charset = trim($matches[1]);
        } else {
            $charset = 'UTF-8';
        }
        //    if( function_exists('mb_convert_encoding') ) {
        //      $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
        //    }

        // Reduce whitespace
        $body = preg_replace('/[\n\r\t\v ]+/', ' ', $body);

        $this->view->title = substr($body, 0, 63);
        $this->view->description = substr($body, 0, 255);
    }

    protected function _previewHtml($uri, Zend_Http_Response $response)
    {

        $body = $response->getBody();
        $body = trim($body);
        if (preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getHeader('content-type'), $matches) ||
            preg_match('/charset=([a-zA-Z0-9-_]+)/i', $response->getBody(), $matches)
        ) {
            $this->view->charset = $charset = trim($matches[1]);
        } else {
            $this->view->charset = $charset = 'UTF-8';
        }
        //    if( function_exists('mb_convert_encoding') ) {
        //      $body = mb_convert_encoding($body, 'HTML-ENTITIES', $charset);
        //    }

        // Get DOM
        if (class_exists('DOMDocument')) {
            $dom = new Zend_Dom_Query($body);
        } else {
            $dom = null; // Maybe add b/c later
        }

        $title = null;
        if ($dom) {
            $titleList = null;
            try {
                $titleList = $dom->query('title');
            } catch (Exception $e) {

            }
            if ($titleList && count($titleList) > 0) {
                $title = trim($titleList->current()->textContent);
                $title = substr($title, 0, 255);
            }
        }
        $this->view->title = $title;

        $description = null;
        if ($dom) {
            $descriptionList = null;
            try {
                $descriptionList = $dom->queryXpath("//meta[@name='description']");
            } catch (Exception $e) {

            }
            // Why are they using caps? -_-
            if ($descriptionList && count($descriptionList) == 0) {
                $descriptionList = $dom->queryXpath("//meta[@name='Description']");
            }
            if (count($descriptionList) > 0) {
                $description = trim($descriptionList->current()->getAttribute('content'));
                $description = substr($description, 0, 255);
            }
        }
        $this->view->description = $description;

        $thumb = null;
        if ($dom) {
            $thumbList = null;
            try {
                $thumbList = $dom->queryXpath("//link[@rel='image_src']");
            } catch (Exception $e) {

            }
            if ($thumbList && count($thumbList) > 0) {
                $thumb = $thumbList->current()->getAttribute('href');
            }
        }
        $this->view->thumb = $thumb;

        $medium = null;
        if ($dom) {
            $mediumList = null;
            try {
                $mediumList = $dom->queryXpath("//meta[@name='medium']");
            } catch (Exception $e) {

            }
            if ($mediumList && count($mediumList) > 0) {
                $medium = $mediumList->current()->getAttribute('content');
            }
        }
        $this->view->medium = $medium;

        // Get baseUrl and baseHref to parse . paths
        $baseUrlInfo = parse_url($uri);
        $baseUrl = null;
        $baseHostUrl = null;
        if ($dom) {
            $baseUrlList = null;
            try {
                $baseUrlList = $dom->query('base');
                if ($baseUrlList && count($baseUrlList) > 0 && $baseUrlList->current()->getAttribute('href')) {
                    $baseUrl = $baseUrlList->current()->getAttribute('href');
                    $baseUrlInfo = parse_url($baseUrl);
                    $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
                }
            } catch (Exception $e) {

            }
        }
        if (!$baseUrl) {
            $baseHostUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/';
            if (empty($baseUrlInfo['path'])) {
                $baseUrl = $baseHostUrl;
            } else {
                $baseUrl = explode('/', $baseUrlInfo['path']);
                array_pop($baseUrl);
                $baseUrl = join('/', $baseUrl);
                $baseUrl = trim($baseUrl, '/');
                $baseUrl = $baseUrlInfo['scheme'] . '://' . $baseUrlInfo['host'] . '/' . $baseUrl . '/';
            }
        }

        $images = array();
        if ($thumb) {
            $images[] = $thumb;
        }
        if ($dom) {
            $imageQuery = null;
            try {
                $imageQuery = $dom->query('img');
            } catch (Exception $e) {
            }

            if ($imageQuery) {
                foreach ($imageQuery as $image) {
                    $src = $image->getAttribute('src');
                    // Ignore images that don't have a src
                    if (!$src || false === ($srcInfo = @parse_url($src))) {
                        continue;
                    }
                    $ext = ltrim(strrchr($src, '.'), '.');
                    // Detect absolute url
                    if (strpos($src, '/') === 0) {
                        // If relative to root, add host
                        $src = $baseHostUrl . ltrim($src, '/');
                    } else if (strpos($src, './') === 0) {
                        // If relative to current path, add baseUrl
                        $src = $baseUrl . substr($src, 2);
                    } else if (!empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
                        // Contians host and scheme, do nothing
                    } else if (empty($srcInfo['scheme']) && empty($srcInfo['host'])) {
                        // if not contains scheme or host, add base
                        $src = $baseUrl . ltrim($src, '/');
                    } else if (empty($srcInfo['scheme']) && !empty($srcInfo['host'])) {
                        // if contains host, but not scheme, add scheme?
                        $src = $baseUrlInfo['scheme'] . ltrim($src, '/');
                    } else {
                        // Just add base
                        $src = $baseUrl . ltrim($src, '/');
                    }
                    // Ignore images that don't come from the same domain
                    //if( strpos($src, $srcInfo['host']) === false ) {
                    // @todo should we do this? disabled for now
                    //continue;
                    //}
                    // Ignore images that don't end in an image extension
                    if (!in_array($ext, array('jpg', 'jpeg', 'gif', 'png'))) {
                        // @todo should we do this? disabled for now
                        //continue;
                    }
                    if (!in_array($src, $images)) {
                        $images[] = $src;
                    }
                }
            }
        }

        // Unique
        $images = array_values(array_unique($images));

        // Truncate if greater than 20
        if (count($images) > 30) {
            array_splice($images, 30, count($images));
        }

        $this->view->imageCount = count($images);
        $this->view->images = $images;
    }
// } Link Controller


// Report Controller {
    public function reportInit()
    {
        $this->_helper->requireUser();
        $this->_helper->requireSubject();
    }

    public function reportCreateAction()
    {
        $subject = Engine_Api::_()->core()->getSubject();

        $form = new Core_Form_Report();
        $form->populate($this->_getAllParams());

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
        $table = Engine_Api::_()->getItemTable('core_report');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            $viewer = Engine_Api::_()->user()->getViewer();

            $report = $table->createRow();
            $report->setFromArray(array_merge($form->getValues(), array(
                'subject_type' => $subject->getType(),
                'subject_id' => $subject->getIdentity(),
                'user_id' => $viewer->getIdentity(),
            )));
            $report->save();

            // Increment report count
            Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.reports');
            if (Engine_Api::_()->apptouch()->isApp()) {
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('ios.core.reports');
            } else {
                Engine_Api::_()->getDbtable('statistics', 'core')->increment('apptouch.core.reports');
            }


            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->redirect($subject, $this->view->translate('Your report has been submitted.'));
    }
// } Report Controller


// Tag Controller {
    public function tagAddAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;
        if (!$this->_helper->requireSubject()->isValid()) return;

        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!method_exists($subject, 'tags')) {
            throw new Engine_Exception('whoops! doesn\'t support tagging');
        }

        // GUID tagging
        if (null !== ($guid = $this->_getParam('guid'))) {
            $tag = Engine_Api::_()->getItemByGuid($this->_getParam('guid'));
        } // STRING tagging
        else if (null !== ($text = $this->_getParam('label'))) {
            $tag = $text;
        }

        $tagmap = $subject->tags()->addTagMap($viewer, $tag, $this->_getParam('extra'));

        if (is_null($tagmap)) {
            // item has already been tagged
            return;
        }

        if (!$tagmap instanceof Core_Model_TagMap) {
            throw new Engine_Exception('Tagmap was not recognised');
        }

        // Do stuff when users are tagged
        if ($tag instanceof User_Model_User && !$subject->isOwner($tag) && !$viewer->isSelf($tag)) {
            // Add activity
            $action = Engine_Api::_()->getDbtable('actions', 'activity')->addActivity(
                $viewer,
                $tag,
                'tagged',
                '', array('is_mobile' => true,
                    'label' => str_replace('_', ' ', $subject->getShortType())
                )
            );
            if ($action) $action->attach($subject);

            // Add notification
            $type_name = $this->view->translate(str_replace('_', ' ', $subject->getShortType()));
            Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification(
                $tag,
                $viewer,
                $subject,
                'tagged',
                array(
                    'object_type_name' => $type_name,
                    'label' => $type_name,
                )
            );
        }

        $this->view->id = $tagmap->getIdentity();
        $this->view->guid = $tagmap->tag_type . '_' . $tagmap->tag_id;
        $this->view->text = $tagmap->getTitle();
        $this->view->href = $tagmap->getHref();
        $this->view->extra = $tagmap->extra;
    }

    public function tagRemoveAction()
    {
        if (!$this->_helper->requireUser()->isValid()) return;
        if (!$this->_helper->requireSubject()->isValid()) return;
        //if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'tag')->isValid() ) return;

        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();

        // Subject doesn't have tagging
        if (!method_exists($subject, 'tags')) {
            throw new Engine_Exception('Subject doesn\'t support tagging');
        }

        // Get tagmao
        $tagmap_id = $this->_getParam('tagmap_id');
        $tagmap = $subject->tags()->getTagMapById($tagmap_id);
        if (!($tagmap instanceof Core_Model_TagMap)) {
            throw new Engine_Exception('Tagmap missing');
        }

        // Can remove if: is tagger, is tagged, is owner of resource, has tag permission
        if ($viewer->getGuid() != $tagmap->tagger_type . '_' . $tagmap->tagger_id &&
            $viewer->getGuid() != $tagmap->tag_type . '_' . $tagmap->tag_id &&
            !$subject->isOwner($viewer) /* &&
        !$subject->authorization()->isAllowed($viewer, 'tag') */
        ) {
            throw new Engine_Exception('Not authorized');
        }

        $tagmap->delete();
    }

    public function tagSuggestAction()
    {
        $tags = Engine_Api::_()->getDbtable('tags', 'core')->getTagsByText($this->_getParam('text'), $this->_getParam('limit', 40));
        $data = array();
        $mode = $this->_getParam('struct');

        if ($mode == 'text') {
            foreach ($tags as $tag) {
                $data[] = $tag->text;
            }
        } else {
            foreach ($tags as $tag) {
                $data[] = array(
                    'id' => $tag->tag_id,
                    'label' => $tag->text
                );
            }
        }

        if ($this->_getParam('sendNow', true)) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }

    public function tagRetrieveAction()
    {
        if (!$this->_helper->requireSubject()->checkRequire()) return;

        $subject = Engine_Api::_()->core()->getSubject();
        $viewer = Engine_Api::_()->user()->getViewer();
        if (!method_exists($subject, 'tags')) {
            throw new Engine_Exception('whoops! doesn\'t support tagging');
        }

        $data = array();
        foreach ($subject->tags()->getTagMaps() as $tagmap) {
            $data[] = array_merge($tagmap->toArray(), array(
                'id' => $tagmap->getIdentity(),
                'text' => $tagmap->getTitle(),
                'href' => $tagmap->getHref(),
                'guid' => $tagmap->tag_type . '_' . $tagmap->tag_id
            ));
        }

        if ($this->_getParam('sendNow', true)) {
            return $this->_helper->json($data);
        } else {
            $this->_helper->viewRenderer->setNoRender(true);
            $data = Zend_Json::encode($data);
            $this->getResponse()->setBody($data);
        }
    }
// } Tag Controller

// Utility Controller {
    public function utilityLocaleAction()
    {
        $locale = $this->_getParam('locale');
        $language = $this->_getParam('language');
        $return = $this->_getParam('return', $this->_helper->url->url(array(), 'default', true));
        $viewer = Engine_Api::_()->user()->getViewer();

        if (!empty($locale)) {
            try {
                $locale = Zend_Locale::findLocale($locale);
            } catch (Exception $e) {
                $locale = null;
            }
        }
        if (!empty($language)) {
            try {
                $language = Zend_Locale::findLocale($language);
            } catch (Exception $e) {
                $language = null;
            }
        }

        if ($language && !$locale) $locale = $language;
        if (!$language && $locale) $language = $locale;

        if ($language && $locale) {
            // Set as cookie
            setcookie('en4_language', $language, time() + (86400 * 365), '/');
            setcookie('en4_locale', $locale, time() + (86400 * 365), '/');
            // Set as database
            if ($viewer && $viewer->getIdentity()) {
                $viewer->locale = $locale;
                $viewer->language = $language;
                $viewer->save();
            }
        }

        return $this->_helper->redirector->gotoUrl($return, array('prependBase' => false));
    }

    public function utilityAdvertisementAction()
    {
        $table = Engine_Api::_()->getDbtable('adcampaigns', 'core');
        $db = $table->getAdapter();
        $db->beginTransaction();

        try {
            if (($adcampaign_id = $this->_getParam('adcampaign_id'))) {
                Engine_Api::_()->getDbtable('adcampaigns', 'core')->update(array(
                    'clicks' => new Zend_Db_Expr('clicks + 1'),
                ), array(
                    'adcampaign_id = ?' => $adcampaign_id,
                ));
            }

            if (($ad_id = $this->_getParam('ad_id'))) {
                Engine_Api::_()->getDbtable('ads', 'apptouch')->update(array(
                    'clicks' => new Zend_Db_Expr('clicks + 1'),
                ), array(
                    'ad_id = ?' => $ad_id,
                ));
            }

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }


// } Utility Controller


// } Search Controller
    public function searchIndexAction()
    {
        $this->addPageInfo('contentTheme', 'd');

        $searchApi = Engine_Api::_()->getApi('search', 'core');

        // check public settings
        $require_check = Engine_Api::_()->getApi('settings', 'core')->core_general_search;
        if (!$require_check) {
            if (!$this->_helper->requireUser()->isValid()) return;
        }

        // Prepare form
        $form =
            new Apptouch_Form_Search();
//      new Core_Form_Search();

        // Get available types
        $availableTypes = $searchApi->getAvailableTypes();
        if (is_array($availableTypes) && count($availableTypes) > 0) {
            $form->addElement('Select', 'type', array(
                'multiOptions' => array(
                    '' => 'Everything',
                ),
                'onchange' => 'if(this.form.search.value)$(this.form).submit();'
            ));

            $options = array();
            foreach ($availableTypes as $index => $type) {
                $options[$type] = strtoupper('ITEM_TYPE_' . $type);
            }
            $form->type->addMultiOptions($options);
        }

        // Check form validity?
        $values = array();
        if ($form->isValid($this->_getAllParams())) {
            $values = $form->getValues();
            $values['query'] = $values['search'];
            unset($values['search']);
        }
        //->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
        $this->setPageTitle($this->view->translate('Search'))
            ->add($this->component()->itemSearch($form));
        $this->view->query = $query = (string)@$values['query'];
        $this->view->type = $type = (string)@$values['type'];
        $this->view->page = $page = (int)$this->_getParam('page');
        if ($query) {
            $this->setPageTitle($this->view->translate('Search') . ' - ' . $values['query']);
            $paginator = $searchApi->getPaginator($query, $type);
            if ($paginator->getTotalItemCount()) {
                $paginator->setCurrentPageNumber($page);
                $this->add($this->component()->itemList($paginator, 'searchResultItemCustomizer', array('listPaginator' => true,)))
//                    ->add($this->component()->paginator($paginator))
                ;
            } else {
                $this->add($this->component()->tip($this->view->translate('No results were found.')));
            }

        } else {
            $this->add($this->component()->tip($this->view->translate('Please enter a search query.')));
        }
        $this->renderContent();
    }

    public function searchResultItemCustomizer(Core_Model_Item_Abstract $item)
    {
        $type = $item->getType();
        $typeName = $this->view->translate('Content Type') . ': ' . $this->view->translate(strtoupper('ITEM_TYPE_' . $type));
        $viewer = Engine_Api::_()->user()->getViewer();
        $owner = $item->getOwner();
        $customize_fields = array(
            'descriptions' => array(
                $typeName,

            )

        );
        if ($type == 'album') {
            $customize_fields['creation_date'] = null;
            $customize_fields['counter'] = strtoupper($this->view->translate(array('%s photo', '%s photos', $item->count()), $this->view->locale()->toNumber($item->count())));

            if ($item->isOwner($viewer))
                $customize_fields = array_merge($customize_fields, array(
                    'manage' => $this->getOptions($item, 'manage', 'album')
                ));
        } elseif ($type == 'album_photo') {
            $customize_fields['descriptions'][] = $this->view->translate('Album') . ': ' . $this->dom()->new_('a', array('href' => $item->getAlbum()->gethref()), $item->getAlbum()->getTitle());
        } elseif ($type == 'blog') {
            $customize_fields['descriptions'][] = $this->view->translate('Posted') . ' ' . $this->view->translate('By') . ' ' . $owner->getTitle();
            $customize_fields['photo'] = $owner->getPhotoUrl('thumb.normal');
            if ($item->isOwner($viewer)) {
                $customize_fields['manage'] = $this->getOptions($item, 'manage', 'blog');

            }
        } elseif ($type == 'classified') {
            $customize_fields['descriptions'][] = $this->view->translate('posted by') . ' ' . $owner->getTitle();
            $customize_fields['photo'] = $owner->getPhotoUrl('thumb.normal');
            if ($item->isOwner($viewer)) {
                $options = array();
                $options[] = $this->getOption($item, 0, 'manage', 'classified');
                $options[] = $this->getOption($item, 1, 'manage', 'classified');
                if (!$item->closed) {
                    $options[] = $this->getOption($item, 2, 'manage', 'classified');
                } else {
                    $options[] = $this->getOption($item, 3, 'manage', 'classified');
                }
                $options[] = $this->getOption($item, 4, 'manage', 'classified');
                $customize_fields['manage'] = $options;

            }
        } elseif ($type == 'classified_album') {
            $customize_fields['descriptions'][] = $this->view->translate('Classified') . ': ' . $this->dom()->new_('a', array('href' => $item->getClassified()->gethref()), $item->getClassified()->getTitle());

        } elseif ($type == 'core_link') {

        } elseif ($type == 'event') {
            $customize_fields['descriptions'][] = $this->view->translate('led by') . ' ' . $owner->getTitle();
            $customize_fields['photo'] = $item->getPhotoUrl('thumb.normal');
            $customize_fields['creation_date'] = $this->view->locale()->toDateTime($item->starttime);
            $customize_fields['counter'] = strtoupper($this->view->translate(array('%s guest', '%s guests', $item->membership()->getMemberCount()), $this->view->locale()->toNumber($item->membership()->getMemberCount())));
            $options = array();

            if ($viewer && !$item->membership()->isMember($viewer, null)) {
                $options[] = $this->getOption($item, 2, 'manage', 'event');
            }
            if ($viewer && $item->membership()->isMember($viewer) && !$item->isOwner($viewer)) {
                $options[] = $this->getOption($item, 3, 'manage', 'event');
            }
            if ($item->isOwner($viewer)) {
                $options[] = $this->getOption($item, 0, 'manage', 'event');
                $options[] = $this->getOption($item, 1, 'manage', 'event');
                $customize_fields['manage'] = $this->getOptions($item, 'manage', 'blog');
            }
        } elseif ($type == 'event_post') {
//      $customize_fields['descriptions'][] = $this->view->translate('APPTOUCH_Topic') . ': ' . $this->dom()->new_('a', array('href' => $item->getParentTopic()->gethref()), $item->getParentTopic()->getTitle());
            $customize_fields['descriptions'][] = $this->view->translate('Event') . ': ' . $this->dom()->new_('a', array('href' => $item->getParentEvent()->gethref()), $item->getParentEvent()->getTitle());

        } elseif ($type == 'event_topic') {
            $customize_fields['descriptions'][] = $this->view->translate('Group') . ': ' . $this->dom()->new_('a', array('href' => $item->getParentEvent()->gethref()), $item->getParentEvent()->getTitle());
        } elseif ($type == 'forum_post') {
//      $customize_fields['descriptions'][] = $this->view->translate('APPTOUCH_Topic') . ': ' . $this->dom()->new_('a', array('href' => $item->getParentTopic()->gethref()), $item->getParentTopic()->getTitle());

            $parentTopic = Engine_Api::_()->getItem('forum_topic', $item->topic_id);
            if ($parentTopic) {
                $customize_fields['descriptions'][] = $this->view->translate('APPTOUCH_Topic') . ': ' . $this->dom()->new_('a', array('href' => $parentTopic->gethref()), $parentTopic->getTitle());
            }
        } elseif ($type == 'forum_topic') {
            $parentForum = Engine_Api::_()->getItem('forum', $item->forum_id);
            if ($parentForum) {
                $customize_fields['descriptions'][] = $this->view->translate('Forum') . ': ' . $this->dom()->new_('a', array('href' => $parentForum->gethref()), $parentForum->getTitle());
            }
        } elseif ($type == 'group') {

        } elseif ($type == 'group_post') {
//      $customize_fields['descriptions'][] = $this->view->translate('APPTOUCH_Topic') . ': ' . $this->dom()->new_('a', array('href' => $item->getParentTopic()->gethref()), $item->getParentTopic()->getTitle());
            $customize_fields['descriptions'][] = $this->view->translate('Group') . ': ' . $this->dom()->new_('a', array('href' => $item->getParentGroup()->gethref()), $item->getParentGroup()->getTitle());

        } elseif ($type == 'group_topic') {
            $customize_fields['descriptions'][] = $this->view->translate('Group') . ': ' . $this->dom()->new_('a', array('href' => $item->getParentGroup()->gethref()), $item->getParentGroup()->getTitle());

        } elseif ($type == 'music_playlist') {

        } elseif ($type == 'page') {

        } elseif ($type == 'pagealbum') {

        } elseif ($type == 'pageblog') {

        } elseif ($type == 'pageevent') {

        } elseif ($type == 'poll') {

        } elseif ($type == 'user') {

        } elseif ($type == 'video') {

        }
        return $customize_fields;
    }

// } Search Controller
    public function renderLangSwitcher()
    {
        // Languages
        $translate = Zend_Registry::get('Zend_Translate');
        $languageList = $translate->getList();

        //$currentLocale = Zend_Registry::get('Locale')->__toString();

        // Prepare default langauge
        $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
        if (!in_array($defaultLanguage, $languageList)) {
            if ($defaultLanguage == 'auto' && isset($languageList['en'])) {
                $defaultLanguage = 'en';
            } else {
                $defaultLanguage = null;
            }
        }

        // Prepare language name list
        $languageNameList = array();
        $languageDataList = Zend_Locale_Data::getList(null, 'language');
        $territoryDataList = Zend_Locale_Data::getList(null, 'territory');

        foreach ($languageList as $localeCode) {
            $languageNameList[$localeCode] = strtoupper($localeCode) . ' - ' . Engine_String::ucfirst(Zend_Locale::getTranslation($localeCode, 'language', $localeCode));
            if (empty($languageNameList[$localeCode])) {
                if (false !== strpos($localeCode, '_')) {
                    list($locale, $territory) = explode('_', $localeCode);
                } else {
                    $locale = $localeCode;
                    $territory = null;
                }
                if (isset($territoryDataList[$territory]) && isset($languageDataList[$locale])) {
                    $languageNameList[$localeCode] = $territoryDataList[$territory] . ' ' . $languageDataList[$locale];
                } else if (isset($territoryDataList[$territory])) {
                    $languageNameList[$localeCode] = $territoryDataList[$territory];
                } else if (isset($languageDataList[$locale])) {
                    $languageNameList[$localeCode] = $languageDataList[$locale];
                } else {
                    continue;
                }
            }
        }
        $languageNameList = array_merge(array(
            $defaultLanguage => $defaultLanguage
        ), $languageNameList);

        $selectedLanguage = $this->view->translate()->getLocale();
        if (1 !== count($languageNameList)) {
            $html = '<form data-mini="true" method="post" action="' . $this->view->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) . '" style="display:inline-block">' .
                $this->view->formSelect('language', $selectedLanguage, array('data-theme' => "b", 'data-mini' => "true", 'data-icon' => "caret-down"), $languageNameList) .
                $this->view->formHidden('return', $this->view->url()) .
                '</form>';
            $this->add($this->component()->html($html), 10, 'header');

            $this->addPageInfo('languageNameList', $languageNameList);
            $this->addPageInfo('selectedLanguage', $selectedLanguage);
        }
    }

}
