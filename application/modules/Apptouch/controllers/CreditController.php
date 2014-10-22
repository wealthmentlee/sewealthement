<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:30
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_CreditController
extends Apptouch_Controller_Action_Bridge
{

//  Index Controller {
  /**
   * @var User_Model_User
   */
  protected $_user;

  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;


  /**
   * @var User_Model_User
   */
  protected $offer_user;

  /**
   * @var Zend_Session_Namespace
   */
  protected $offer_session;

  /**
   * @var Offers_Model_Order
   */
  protected $offer_order;

  /**
   * @var Payment_Model_Gateway
   */
  protected $offer_gateway;

  /**
   * @var Offers_Model_Subscription
   */
  protected $offer_subscription;

  /**
   * @var Offers_Model_Offer
   */
  protected $offer_offer;


  public function buyLevelInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('payment')) {
      $this->redirect($this->view->url(array(), 'credit_general', true));
    }

    // Get user and session
    $this->_user = Engine_Api::_()->user()->getViewer();
    $this->_session = new Zend_Session_Namespace('Payment_Subscription');

    // Check viewer and user
    if( !$this->_user || !$this->_user->getIdentity() ) {
      $this->redirect($this->view->url(array(), 'credit_general', true));
    }
  }

  public function buyLevelDetailsAction()
  {
    $this->view->error = false;
    $package_id = $this->_getParam('package_id', 0);
    // Get package
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $this->view->package = $package = $packagesTable->fetchRow(array('enabled = ?' => true, 'package_id = ?' => $package_id));
    if (!$package) {
      $this->view->error = true;
      return ;
    }
    $this->view->packageDescription = Engine_Api::_()->credit()->getPackageDescription($package);
  }

  public function buyLevelConfirmAction()
  {
    $package_id = $this->_getParam('package_id', 0);
    if ($package_id) {
      $this->_helper->layout->setLayout('default-simple');
    }
    // Process
    $user = Engine_Api::_()->user()->getViewer();
    $this->view->result = true;

    if (!$package_id && isset($this->_session->subscription_id)) {
      $subscription = Engine_Api::_()->getItem('payment_subscription', $this->_session->subscription_id);
      $package_id = $subscription->package_id;
      $this->view->cancel_url = Zend_Controller_Front::getInstance()->getRouter()
        ->assemble(
          array(
            'action' => 'index',
            'controller' => 'settings',
            'module' => 'payment'
          ), 'default', true);
    }

    // Get packages
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $this->view->package = $package = $packagesTable->fetchRow(array(
      'enabled = ?' => 1,
      'package_id = ?' => $package_id,
    ));

    // Check if it exists
    if( !$package ) {
      $this->view->message = Zend_Registry::get('Zend_View')->translate('Please choose one now below.');
      return ;
    }

    $level = Engine_Api::_()->getItem('authorization_level', $this->_user->level_id);
    if( in_array($level->type, array('admin', 'moderator')) ) {
      $this->view->message = Zend_Registry::get('Zend_View')->translate('Subscriptions are not required for administrators and moderators.');
      return ;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $defaultPrice = $settings->getSetting('credit.default.price', 100);
    $credits = ceil($package->price * $defaultPrice);

    $balance = Engine_Api::_()->getItem('credit_balance', $this->_user->getIdentity());
    if (!$balance) {
      $currentBalance = 0;
    } else {
      $currentBalance = $balance->current_credit;
    }
    $this->view->currentBalance = $currentBalance;
    $this->view->enoughCredits = $this->_checkEnoughCredits($credits);
    $this->view->packageDescription = Engine_Api::_()->credit()->getPackageDescription($package);

    // Get current subscription and package
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
    $currentSubscription = $subscriptionsTable->fetchRow(array(
      'user_id = ?' => $user->getIdentity(),
      'active = ?' => true,
    ));

    // Get current package
    $currentPackage = null;
    if( $currentSubscription ) {
      $currentPackage = $packagesTable->fetchRow(array(
        'package_id = ?' => $currentSubscription->package_id,
      ));
    }

    // Check if they are the same
    if( $currentPackage && $package->package_id == $currentPackage->package_id ) {
      return $this->_helper->redirector->gotoRoute(array(), 'credit_general', true);
    }

    // Check method
    if( !$this->getRequest()->isPost() ) {
      return;
    }

    // Cancel any other existing subscriptions
    Engine_Api::_()->getDbtable('subscriptions', 'payment')
      ->cancelAll($user, 'User cancelled the subscription.', $currentSubscription);


    // Insert the new temporary subscription
    $db = $subscriptionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $subscription = $subscriptionsTable->createRow();
      $subscription->setFromArray(array(
        'package_id' => $package->package_id,
        'user_id' => $user->getIdentity(),
        'status' => 'initial',
        'active' => false, // Will set to active on payment success
        'creation_date' => new Zend_Db_Expr('NOW()'),
      ));
      $subscription->save();

      // If the package is free, let's set it active now and cancel the other
      if( $package->isFree() ) {
        $subscription->setActive(true);
        $subscription->onPaymentSuccess();
        if( $currentSubscription ) {
          $currentSubscription->cancel();
        }
      }

      $subscription_id = $subscription->subscription_id;

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    // Check if the subscription is ok
    if( $package->isFree() && $subscriptionsTable->check($user) ) {
      return $this->_helper->redirector->gotoRoute(array(), 'credit_general', true);
    }

    // Prepare subscription session
    $session = new Zend_Session_Namespace('Payment_Subscription');
    $session->is_change = true;
    $session->user_id = $user->getIdentity();
    $session->subscription_id = $subscription_id;

    // Redirect to subscription handler
    return $this->_helper->redirector->gotoRoute(array('action' => 'process'));
  }

  public function buyLevelProcessAction()
  {
    // Get gateway
    $this->view->gateway = $gateway = Engine_Api::_()->getDbTable('gateways', 'payment')->fetchRow(array('title = ?' => 'Testing'));

    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if( !$subscriptionId ||
        !($subscription = Engine_Api::_()->getItem('payment_subscription', $subscriptionId))  ) {
      return $this->_helper->redirector->gotoRoute(array(), 'credit_general', true);
    }
    /**
     * @var $subscription Payment_Model_Subscription
     */
    $this->view->subscription = $subscription;

    // Get package
    $package = $subscription->getPackage();
    if( !$package || $package->isFree() ) {
      return $this->_helper->redirector->gotoRoute(array(), 'credit_general', true);
    }
    $this->view->package = $package;

    // Check subscription?
    if( $this->_checkSubscriptionStatus($subscription) ) {
      return;
    }

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $defaultPrice = $settings->getSetting('credit.default.price', 100);
    $credits = ceil($package->price * $defaultPrice);

    if (!$this->_checkEnoughCredits($credits)) {
      $this->redirect(Zend_Controller_Front::getInstance()
          ->getRouter()
          ->assemble(
            array(),
            'credit_general', true
          ),
        Zend_Registry::get('Zend_Translate')->_('CREDIT_not-enough-credit'));
    }

    // Process

    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
    if( !empty($this->_session->order_id) ) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if( $previousOrder && $previousOrder->state == 'pending' ) {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    $ordersTable->insert(array(
      'user_id' => $this->_user->getIdentity(),
      'gateway_id' => $gateway->gateway_id,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => 'payment_subscription',
      'source_id' => $subscription->subscription_id,
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    Engine_Api::_()->credit()->buyLevel($this->_user, (-1)*$credits, $package->getTitle());
    $order = Engine_Api::_()->getItem('payment_order', $order_id);
    $order->state = 'complete';
    $order->save();
    $subscription->onPaymentSuccess();

    $this->_finishPayment('active');
  }

  protected function _checkSubscriptionStatus(
      Zend_Db_Table_Row_Abstract $subscription = null)
  {
    if( !$this->_user ) {
      return false;
    }

    if( null === $subscription ) {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
      $subscription = $subscriptionsTable->fetchRow(array(
        'user_id = ?' => $this->_user->getIdentity(),
        'active = ?' => true,
      ));
    }

    if( !$subscription ) {
      return false;
    }

    if( $subscription->status == 'active' ||
        $subscription->status == 'trial' ) {
      if( !$subscription->getPackage()->isFree() ) {
        $this->_finishPayment('active');
      } else {
        $this->_finishPayment('free');
      }
      return true;
    } else if( $subscription->status == 'pending' ) {
      $this->_finishPayment('pending');
      return true;
    }

    return false;
  }

  protected function _checkEnoughCredits($credits)
  {
    $balance = Engine_Api::_()->getItem('credit_balance', $this->_user->getIdentity());
    if (!$balance) {
      return false;
    }
    $currentBalance = $balance->current_credit;
    if ($currentBalance < $credits) {
      return false;
    }
    return true;
  }

  protected function _finishPayment($state = 'active')
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // No user?
    if( !$this->_user ) {
      return $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }

    // Log the user in, if they aren't already
    if( ($state == 'active' || $state == 'free') &&
        $this->_user &&
        !$this->_user->isSelf($viewer) &&
        !$viewer->getIdentity() ) {
      Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
      Engine_Api::_()->user()->setViewer();
      $viewer = $this->_user;
    }

    // Handle email verification or pending approval
    if( $viewer->getIdentity() && !$viewer->enabled ) {
      Engine_Api::_()->user()->setViewer(null);
      Engine_Api::_()->user()->getAuth()->getStorage()->clear();

      $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
      $confirmSession->approved = $viewer->approved;
      $confirmSession->verified = $viewer->verified;
      $confirmSession->enabled  = $viewer->enabled;
      return $this->_helper->_redirector->gotoRoute(array('action' => 'confirm'), 'user_signup', true);
    }

    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $userIdentity = $this->_session->user_id;
    $this->_session->unsetAll();
    $this->_session->user_id = $userIdentity;
    $this->_session->errorMessage = $errorMessage;

    // Redirect
    if( $state == 'free' ) {
            $this->redirect($this->view->url(array(), 'credit_general', true));
    } else {
            $this->redirect($this->view->url(array('action' => 'finish', 'state' => $state)));
    }
  }

  public function buyLevelFinishAction()
  {
    $this->view->status = $status = $this->_getParam('state');
    $this->view->error = $this->_session->errorMessage;
  }

//  Index Controller {


//  Index Controller {
  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');
  }
  public function indexIndexAction()
  {
//    if( !$this->_helper->requireAuth()->setAuthParams('credit', null, 'view_credit_home')->isValid() ) return;
      $this
        ->widgetFaq()
        ->widgetBrowseMembers()
        ->renderContent();
  }

  protected function widgetFaq()
  {
    $translate = Zend_Registry::get('Zend_Translate');

    $faqs = array();
    $iter = 1;
    while('CREDIT_ANSWER_'.$iter != $translate->_('CREDIT_ANSWER_'.$iter)) {
      $faqs[$iter] = $translate->_('CREDIT_ANSWER_'.$iter);
      $iter ++;
    }

    if ($iter == 1) {
      return $this;
    }


    $faq = $faqs[rand(1, $iter-1)];

    $faqEl = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'e', 'data-theme' => 'e', 'data-collapsed' => false), '', array(
      $this->dom()->new_('h3', array(), $this->view->translate('FAQ')),
      $this->dom()->new_('p', array(), $this->view->string()->truncate($faq, 150, '... '.$this->view->htmlLink($this->view->url(array('action' => 'faq'), 'credit_general', true), $this->view->translate('more'), array('target' => '_blank')))),

    ));
    return $this->add($this->component()->html($faqEl));
  }

  protected function widgetBrowseMembers()
  {
    $page = $this->_getParam('page', 1);
    $table = Engine_Api::_()->getDbTable('balances', 'credit');
    $top_users = Zend_Paginator::factory($table->getTopUsersSelect($page));
    $top_users->setCurrentPageNumber($page);
    return $this->setFormat('browse')
      ->add($this->topUsers($top_users))
      ->add($this->component()->paginator($top_users));
  }

  private function topUsers($top_users)
  {
   $component = array();
    $items = array();

    foreach( $top_users as $item ){
      $user = $this->view->item('user', $item->balance_id);
      $photoUrl = $user->getPhotoUrl('thumb.normal');
      if(!isset($user->photo_id) && !isset($user->file_id)){
        $photoUrl = false;
      }
      $credits = $this->dom()->new_('div', array('class' => 'ui-grid-a'), '', array(
        $this->dom()->new_('div', array('class' => 'ui-block-a', 'style' => 'line-height: 20px;'), $this->view->translate('Earned Credits') . ':'),
        $this->dom()->new_('div', array('class' => 'ui-block-b', 'style' => 'text-align: right; line-height: 20px; padding-right: 25px;'), $this->view->locale()->toNumber($item->earned_credit)),
        $this->dom()->new_('div', array('class' => 'ui-block-a', 'style' => 'line-height: 20px;'), $this->view->translate('Spent Credits') . ':'),
        $this->dom()->new_('div', array('class' => 'ui-block-b', 'style' => 'text-align: right; line-height: 20px; padding-right: 25px;'), $this->view->locale()->toNumber($item->spent_credit))
      ));
      $listItem = array(
        'title' => '<b class="credit-top-user-place ui-btn-up-e ui-corner-all">' . $this->view->locale()->toNumber($item->place) . '</b> ' . $user->getTitle(),
        'descriptions' => array(
          $credits . ''
        ),
        'href' => $user->getHref(),
        'photo' => $photoUrl,
        'counter' => strtoupper(/*$this->view->translate('APPTOUCH_Balance') . ' ' . */$this->view->locale()->toNumber($item->current_credit))
      );
      $items[] = $listItem;
    }
    $component['items'] = $items;

    return $this->component()->customComponent('itemList', $component);
  }

  public function indexManageAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()) {
      $this->_helper->redirector->gotoRoute(array(), 'credit_general', true);
    }
    $this
      ->setFormat('browse')
      ->add($this->component()->html($this->dom()->new_('div', array('data-role' => 'collapsible-set'), '', array())))
      ->widgetCreateItems()
      ->widgetMyCredits()
//      ->widgetSendCredits()
      ->widgetTransactionList()
      ->renderContent();
  }

  protected function widgetMyCredits($alone = true)
  {
    if (Engine_Api::_()->core()->hasSubject('user')) {
       $viewer_id = Engine_Api::_()->core()->getSubject('user')->getIdentity();
     } else {
       $viewer_id = Engine_Api::_()->user()->getViewer()->getIdentity();
     }
     if (!$viewer_id) {
       return $this->setNoRender();
     }
     $credits = Engine_Api::_()->getItem('credit_balance', $viewer_id);

     /**
      * @var $table Credit_Model_DbTable_Balances
      */

     $table = Engine_Api::_()->getDbTable('balances', 'credit');
     $users = $table->fetchAll($table->getTopUsersSelect());

     $place = null;
     foreach ($users as $user) {
       if ($user->balance_id == $viewer_id) {
         $place = $user->place;
         break;
       }
     }

     $all_users = count($users);
     $point = (double)$all_users/5.0;
     $icon = 5;

     if ($all_users < 5) {
       $icon = $place;
     } else {
       for ($i = 0; $i < 5; $i ++) {
         $first = (double)$i*$point;
         $second = (double)($i+1)*$point;
         if ($place > $first && $place <= $second) {
           $icon = $i+1;
           break;
         }
       }
     }

    $myCredits = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'c', 'data-collapsed' => false), '', array(
          $this->dom()->new_('h3', array(), $this->view->translate('My Credits')),
          $this->dom()->new_('p', array(), '', array(
            $this->dom()->new_('p', array(/*'class' => 'ui-bar-b ui-corner-all'*/), $this->view->translate('CREDIT_My Credits Description')),
            $this->dom()->new_('div', array('data-role' => 'controlgroup'), '', array(
                $this->dom()->new_('div', array('data-role' => 'button', 'data-theme' => 'e', 'data-iconpos' => 'top', 'data-icon' => 'star'), $this->view->translate('Your Place %s', ($place) ? $this->view->locale()->toNumber($place) : $this->view->translate('unknown'))),
                $this->dom()->new_('div', array('data-role' => 'button', 'data-icon' => 'star'), $this->view->locale()->toNumber($credits->current_credit) . ' ' . $this->view->translate('Current Balance')),
                $this->dom()->new_('div', array('data-role' => 'button', 'data-icon' => 'plus'), $this->view->locale()->toNumber($credits->earned_credit) . ' ' . $this->view->translate('Earned Credits')),
                $this->dom()->new_('div', array('data-role' => 'button', 'data-icon' => 'minus'), $this->view->locale()->toNumber($credits->spent_credit) . ' ' . $this->view->translate('Spent Credits')),
            ))
          )),

        ));
    if($alone)
      return $this->add($this->component()->html($myCredits));
    else
      return $myCredits;
  }

  protected function widgetSendCredits($alone = true)
  {
    $user = Engine_Api::_()->user()->getViewer();
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $user_id = $this->_getParam('user_id', 0);
    if (!$user->getIdentity() || $permissionsTable->getAllowed('credit', $user->level_id, 'transfer') === 0) {
      return $alone ? $this : null;
    }
    $form = new Credit_Form_Send('smoothbox', $user_id);
    $form->getElement('username')->setDescription('');
    $form->setAction($this->view->url(array('action' => 'send'), 'credit_general', true));
    $sendCredits = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'c', 'data-collapsed' => false), '', array(
      $this->dom()->new_('h3', array(), $this->view->translate('Send Credits')),
      $this->dom()->new_('p', array(), $form->render($this->view))
      ));
    $this->addPageInfo('credit', array(
      'suggestUrl' => $this->view->url(array('action' => 'suggest'), 'credit_general', true)
    ));
    if($alone)
      return $this->add($this->component()->html($sendCredits));
    else
      return $sendCredits;
  }

  protected function widgetTransactionList($alone = true)
  {
    $page = $this->_getParam('page', 1);
    $user = Engine_Api::_()->user()->getViewer();
    if (!$user->getIdentity()) {
      return $alone ? $this : null;
    }
    $paginator = Engine_Api::_()->getDbTable('logs', 'credit')
      ->getTransaction(
        array(
          'page' => $page,
          'user_id' => $user->getIdentity()
        )
      );

    $transactionListEl = $this->dom()->new_('div', array('class' =>'ui-grid-b credit-transaction-list'), '', array(
      $this->dom()->new_('div', array('class' => 'ui-block-a ui-bar-a'), $this->view->translate("Action Date")),
      $this->dom()->new_('div', array('class' => 'ui-block-b ui-bar-a'), $this->view->translate("Action Type")),
      $this->dom()->new_('div', array('class' => 'ui-block-c ui-bar-a'), $this->view->translate("Credit")),
    ));

    foreach( $paginator as $item ){
      if ($item->object_type == null) {
        $actionType = $this->view->translate($item->action_name, $item->body);
      } else {
        if (!Engine_Api::_()->credit()->isModuleEnabled($item->action_module)) {
          if ($item->body) {
            $actionType = $this->view->translate($item->action_name, $item->body, '<i style="color: red">'.$this->view->translate('Plugin Disabled').'</i>');
          } else {
            $actionType = $this->view->translate($item->action_name, '<i style="color: red">'.$this->view->translate('Plugin Disabled').'</i>');
          }
        } else {
          if (($object = $this->view->item($item->object_type, $item->object_id)) !== null) {
            if ($item->object_type == 'answer') {
              $uri = $object->getHref();
              $href = $uri['uri'];
            } else {
              $href = $object->getHref();
            }
            if ($item->body) {
              $actionType = $this->view->translate($item->action_name, $item->body, $this->view->htmlLink($href, ($object->getTitle())?$object->getTitle():$this->view->translate('click here'), array('target' => '_blank')));
            } else {
              $actionType = $this->view->translate($item->action_name, $this->view->htmlLink($href, ($object->getTitle())?$object->getTitle():$this->view->translate('click here'), array('target' => '_blank')));
            }
          } else {
            if ($item->body) {
              $actionType = $this->view->translate($item->action_name, $item->body, '<i style="color: red">'.$this->view->translate('Deleted').'</i>');
            }  else {
              $actionType = $this->view->translate($item->action_name, '<i style="color: red">'.$this->view->translate('Deleted').'</i>');
            }
          }
        }
      }
      $transactionListEl->append($this->dom()->new_('div', array('class' => 'ui-block-a ui-bar-c'), $this->view->timestamp($item->creation_date)));
      $transactionListEl->append($this->dom()->new_('div', array('class' => 'ui-block-b ui-bar-c'), $actionType));
      $transactionListEl->append($this->dom()->new_('div', array('class' => 'ui-block-c ui-bar-c'), $this->view->locale()->toNumber($item->credit)));

    }
    $transactionEl = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'c'), '', array(
      $this->dom()->new_('h3', array(), $this->view->translate('Transaction List')),
      $this->dom()->new_('p', array(), '', array($transactionListEl))
    ));
    $transactionEl->attr('data-collapsed', false/*$this->_getParam('uncollapse') != 'transaction-list'*/);
    if($alone)
      return $this
        ->add($this->component()->html($transactionEl))
        ->add($this->component()->paginator($paginator));
    else
      return $transactionEl;
  }

  protected function widgetCreateItems($alone = true)
  {
    $user = Engine_Api::_()->user()->getViewer();

    if (!$user->getIdentity()) {
      return $this->setNoRender();
    }

    $modules = array(
     //module => action type
      'album' => 'album_photo_new',
      'article' => 'article_new',
      'blog' => 'blog_new',
      'event' => 'event_create',
      'forum' => 'forum_topic_create',
      'group' => 'group_create',
      'inviter' => 'invite',
      'music' => 'music_playlist_song',
      'page' => 'page_create',
      'poll' => 'poll_new',
      'video' => 'video'
    );

    /**
     * @var $modulesTbl Core_Model_DbTable_Modules
     * @var $creditsTbl Credit_Model_DbTable_Logs
     * @var $actionTypesTbl Credit_Model_DbTable_ActionTypes
     * @var $menusApi Core_Api_Menus
     */

    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $creditsTbl = Engine_Api::_()->getDbTable('logs', 'credit');
    $actionTypesTbl = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $menusApi = Engine_Api::_()->getApi('menus', 'core');
    $navigation = new Zend_Navigation();

    foreach ($modules as $module => $action_type) {
      if (
        !$modulesTbl->isModuleEnabled($module) ||
        !$creditsTbl->checkCredit($actionTypesTbl->getActionType($action_type), $user)
      ) {
        continue;
      }

      if ($module == 'page' || $module == 'inviter' || $module == 'forum') {
        $pages = array($this->getNav($module));
      } else {
        $pages = $menusApi->getMenuParams($module.'_quick');
      }
      $navigation->addPages($pages);
    }


    if (!count($navigation)) {
      return $alone ? $this : null;
    }
    return $this->add($this->component()->quickLinks($navigation, $this->view->translate('Quick Links')));
  }

  private function getNav($module)
  {
    if ($module == 'page') {
      return array(
        'route'  =>  'page_create',
        'action' =>  'create',
        'class'  =>  'buttonlink  icon_page_new  menu_page_quick  page_quick_create',
        'label'  =>  'Create New Page',
        'reset_params'  =>  1
      );
    } elseif ($module == 'forum') {
      return array(
        'route'  =>  'forum_general',
        'class'  =>  'buttonlink  icon_forum_post_new  menu_forum_quick  forum_quick_create',
        'label'  =>  'Post New Topic',
        'reset_params'  =>  1
      );
    } elseif ($module == 'inviter') {
      return array(
        'route'  =>  'inviter_general',
        'class'  =>  'buttonlink  icon_invite  menu_invite_quick  inviter_quick_invite',
        'label'  =>  'Invite Friends',
        'reset_params'  =>  1
      );
    }
  }

  public function indexSendAction()
  {
    $notificationTable = Engine_Api::_()->getDbTable('notifications', 'activity');
    $translate = Zend_Registry::get('Zend_Translate');

    $credits = abs((int)$this->_getParam('credit', 0));
    $user_id = $this->_getParam('user_id', 0);
    $form = new Credit_Form_Send('smoothbox', $user_id);
    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
      ->renderContent();
      return false;
    }

    if (empty($user_id)) {
      $this->view->result  = false;
      $this->view->message = $translate->_('Sorry, this User doesn\'t exist, please, choose from suggestion list!');
      $this->add($this->component()->form($form))
      ->renderContent();
      return ;
    }

    if (empty($credits) || $credits <= 0) {
      $this->view->result  = false;
      $this->view->message = $translate->_('Sorry, you didn\'t enter credits');
      $this->add($this->component()->form($form))
      ->renderContent();
      return ;
    }

    $sender = Engine_Api::_()->user()->getViewer();

    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    if ($permissionsTable->getAllowed('credit', $sender->level_id, 'transfer') === 0) {
      $this->view->result  = false;
        $this->view->message = $translate->_('Sorry, Admin doesn\'t allow to transfer for this level.');
      $this->add($this->component()->form($form))
      ->renderContent();
      return ;
    }

    if (!$this->getAllowTransfer($sender->getIdentity())) {
      $this->view->result  = false;
      $this->view->message = $translate->_('Sorry, Admin doesn\'t allow to transfer for you, you have reached the daily limit.');
      $this->add($this->component()->form($form))
      ->renderContent();
      return ;
    }

    $recipient = Engine_Api::_()->getItem('user', $user_id);

    /**
     * @var $creditApi Credit_Api_Core
     **/

    $creditApi = Engine_Api::_()->credit();
    $value = Engine_Api::_()->getItem('credit_balance', $sender->getIdentity())->current_credit - $credits;
    if ($value < 0) {
      $this->view->result  = false;
      $this->view->message = $translate->_('CREDIT_not-enough-credit');
      $this->add($this->component()->form($form))
      ->renderContent();
      return;
    }

    $creditApi->updateTransferCredits($sender, $recipient, $credits);

    $notificationTable->addNotification($recipient, $sender, $recipient, 'send_credits', array(
      'amount' => $credits,
      'action' => $this->view->url(array('action' => 'manage'), 'credit_general', true),
      'label' => $translate->_('here'),
    ));

    $this->view->result  = true;

      $this->view->message = $this->view->translate('%s credits successfully sent to your friend %s', $credits, $recipient->getTitle());
      return $this->refresh();
  }

  public function indexBuyAction()
  {
    /**
     * @var $table Credit_Model_DbTable_Payments
     */
    $this->_helper->layout->setLayout('default-simple');
    $table = Engine_Api::_()->getDbTable('payments', 'credit');

    $this->view->prices = $prices = $table->getPrices();
    $price = empty($prices[0]->credit) ? null : $prices[0];
    $this->view->credits_for_one_unit = ($price) ? (float)($price->credit/(float)$price->price) : 0; // credits for one unit
    $this->view->currency = Engine_Api::_()->getApi('settings', 'core')->getSetting('payment.currency', 'USD');

    $user_id = Engine_Api::_()->user()->getViewer()->getIdentity();
    $balance = Engine_Api::_()->getItem('credit_balance', $user_id);
    $this->view->current_balance = $balance->current_credit;
  }

  public function indexFaqAction()
  {
//    if( !$this->_helper->requireAuth()->setAuthParams('credit', null, 'view_credit_faq')->isValid() ) return;

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_main', array(), 'credit_main_faq');

    $translate = Zend_Registry::get('Zend_Translate');

    $faqs = array();
    $iter = 1;
    while('CREDIT_QUESTION_'.$iter != $translate->_('CREDIT_QUESTION_'.$iter)) {
      if ('CREDIT_ANSWER_'.$iter !=$translate->_('CREDIT_ANSWER_'.$iter)) {
        $faqs[$iter]['q'] = 'CREDIT_QUESTION_'.$iter;
        $faqs[$iter]['a'] = 'CREDIT_ANSWER_'.$iter;
      }
      $iter ++;
    }
//    $this->view->faqs = $faqs;
    $faqsEl = array();
    foreach($faqs as $key => $faq){
      $faqEl = $this->dom()->new_('div', array('data-role' => 'collapsible'), '', array(
        $this->dom()->new_('h3', array(), $this->view->translate($faq['q'])),
        $this->dom()->new_('p', array(), $this->view->translate($faq['a']))
      ));
      $faqsEl[] = $faqEl;
    }
    /**
     * @var $table Credit_Model_DbTable_ActionTypes
     */

    $table = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $actionTypes = $table->getActionTypes(array('action_module' => 'ASC', 'credit' => 1));
    $actionTypesEl = $this->dom()->new_('div', array('class' =>'ui-grid-c credit-at'), '', array(
      $this->dom()->new_('div', array('class' => 'ui-block-a ui-bar-a'), $this->view->translate("Action Type")),
      $this->dom()->new_('div', array('class' => 'ui-block-b ui-bar-a'), $this->view->translate("Credit")),
      $this->dom()->new_('div', array('class' => 'ui-block-c ui-bar-a'), $this->view->translate("Max Credit")),
      $this->dom()->new_('div', array('class' => 'ui-block-d ui-bar-a'), $this->view->translate("Rollover Period"))
    ));
    foreach( $actionTypes as $key => $type){
      if ($key){
        $is_module = ($type->action_module != $actionTypes[$key-1]->action_module); $type = $actionTypes[$key];
        if ($is_module)
          $actionTypesEl
            ->append($this->dom()->new_('div', array('class' => 'ui-block-a ui-bar-b credit-at-module'), ucfirst($this->view->translate('_CREDIT_'.$type->action_module))));
      } else
        $actionTypesEl
          ->append($this->dom()->new_('div', array('class' => 'ui-block-a ui-bar-b credit-at-module'), ucfirst($this->view->translate('_CREDIT_'.$type->action_module))));

        $actionTypesEl
          ->append($this->dom()->new_('div', array('class' => 'ui-block-a ui-bar-c'), $this->view->translate('_CREDIT_ACTION_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type->action_type), '_')))))
          ->append($this->dom()->new_('div', array('class' => 'ui-block-b ui-bar-c'), $this->view->locale()->toNumber($type->credit)))
          ->append($this->dom()->new_('div', array('class' => 'ui-block-c ui-bar-c'), $this->view->locale()->toNumber($type->max_credit)))
          ->append($this->dom()->new_('div', array('class' => 'ui-block-d ui-bar-c'), ($type->rollover_period) ? $this->view->locale()->toNumber($type->rollover_period) . $this->view->translate(' day(s)') : $this->view->translate('never')));


    }
    $this->setFormat('browse')
      ->setPageTitle($this->view->translate('Credits'), $this->view->translate('Credits'))
      ->add($this->component()->html($faqsEl))
      ->add($this->component()->html(array(
        $this->dom()->new_('h3', array(), $this->view->translate('CREDIT_FAQ_ACTION_TYPES_DESCRIPTION')),
        $actionTypesEl
      )))
      ->renderContent();
  }

  public function indexSuggestAction()
  {
    $users = $this->getUsersByText($this->_getParam('text'), $this->_getParam('limit', 40));
    $data = array();
    $mode = $this->_getParam('struct');

    if( $mode == 'text' ) {
      foreach( $users as $user ) {
        $data[] = $user->displayname;
      }
    } else {
      foreach( $users as $user ) {
        if (!$this->getAllowTransfer($user->user_id)) {
          continue;
        }
        $data[] = array(
          'id' => $user->user_id,
          'label' => $user->displayname,
          'photo' => $this->view->itemPhoto($user, 'thumb.icon')
        );
      }
    }

    if( $this->_getParam('sendNow', true) ) {
      return $this->_helper->json($data);
    } else {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }
  }


  public function buyOfferInit()
  {
    $this->addPageInfo('contentTheme', 'd');

    $this->add($this->component()->navigation('credit_main', true));

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('offers')) {
      return $this->redirect($this->view->url(array(), 'credit_general', true));
    }

    // Get user and session
    $this->offer_user = Engine_Api::_()->user()->getViewer();
    $this->offer_session = new Zend_Session_Namespace('Offer_Subscription');

    // Get offer
    $offerId = $this->_getParam('offer_id', $this->_getParam('offer_id', $this->offer_session->offer_id));
    if (!$offerId || !($this->offer_offer = Engine_Api::_()->getItem('offer', $offerId))) {
      $this->_goBack(false);
    }

    if (!$this->offer_offer || !$this->offer_offer->getPrice()) {
      return $this->_goBack(false);
    }

    if (!($this->offer_offer->getCouponsCount() || $this->offer_offer->coupons_unlimit)) {
      return $this->_goBack();
    }

    if ($this->offer_offer->isSubscribed($this->offer_user)) {
      return $this->_goBack();
    }

    if (!$this->offer_offer->isOfferCredit()) {
      return $this->_goBack();
    }

    // Check viewer and user
    if (!$this->offer_user || !$this->offer_user->getIdentity()) {
      return $this->_goBack();
    }

    // Check subscription status
    if ($this->_checkOfferStatus()) {
      return $this->_goBack();
    }
  }

  public function buyOfferIndexAction()
  {
    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->offer_session->subscription_id);
    if (!$subscriptionId ||
      !($subscription = Engine_Api::_()->getItem('offers_subscription', $subscriptionId))) {
      return $this->_goBack();
    }

    // Get package
    $offer = $subscription->getOffer();
    $this->offer_session->offer_id = $offer->getIdentity();

    // Process
    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'offers');
    if (!empty($this->offer_session->order_id)) {
      $previousOrder = $ordersTable->find($this->offer_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    $ordersTable->insert(array(
      'user_id' => $this->offer_user->getIdentity(),
      'gateway_id' => 0,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => 'offers_subscription',
      'source_id' => $subscription->subscription_id,
    ));

    $this->offer_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();
    $order = Engine_Api::_()->getItem('offers_order', $order_id);
    $credits = Engine_Api::_()->getItem('credit_balance', $this->offer_user->getIdentity());

    if ($credits->current_credit < Engine_Api::_()->offers()->getCredits($offer->price_offer)) {
      $this->add($this->component()->html('<h3>' . $this->view->translate('CREDIT_not-enough-credit') . '</h3>'));
    }

    $title = '<h3>' . $this->view->htmlLink($offer->getHref(), $this->view->string()->truncate($offer->getTitle(), 20)) . '</h3>';
    $text = '<b>'. $this->view->translate('OFFERS_offer_price') . '</b>  ' . $this->view->getOfferPrice($offer) . '<br>';
    $text .= '<b>'. $this->view->translate('OFFERS_offer_discount') . '</b>  ' . $offer->discount . (($offer->discount_type == 'percent') ? '%' : '' ). '<br>';
    $text .= '<b>'. $this->view->translate('OFFERS_offer_available') . '</b>  ' . ((($offer->coupons_unlimit) || ($offer->coupons_count > 0)) ? (($offer->coupons_unlimit) ? $this->view->translate('unlimit coupons') : $this->view->translate('%s coupons', $offer->coupons_count)) : $this->view->translate('OFFERS_offer_not_left')) . '<br>';
    $text .= '<b>'. $this->view->translate('OFFERS_Redeem') . '</b>  ' . Engine_Api::_()->offers()->timeInterval($offer) . '<br>';
    $body = '<p>' . $text . '</p>';
    $element = '<div data-role="collapsible" data-content-theme="c">' . $title . $body . '</div>';
    $data = array();
    $data['items'] = array();
    $data['items'][] = array(
      'descriptions' => array($element),
      'href' => $offer->getHref(),
      'photo' => $offer->getPhotoUrl('thumb.normal'),
    );

    $this->add($this->component()->customComponent('itemList', $data))
      ->add($this->component()->html('<h3>' . $this->view->translate('Order Summary') . '</h3>'))
      ->add($this->component()->html('<b>' . $this->view->translate('Current Balance') . '</b> ' . $this->view->locale()->toNumber($credits->current_credit)))
      ->add($this->component()->html('<b>' . $this->view->translate('OFFERS_Price') . '</b> ' . $this->view->locale()->toNumber(Engine_Api::_()->offers()->getCredits($offer->price_offer))))
    ;

    if ($credits->current_credit >= Engine_Api::_()->offers()->getCredits($offer->price_offer)) {
      $confirm = $this->dom()->new_('a', array(
        'data-role' => 'button',
        'href' => $this->view->url(array('module' => 'credit', 'controller' => 'buy-offer', 'action' => 'pay', 'status' => 'continue'), 'default', true)
      ), $this->view->translate('Confirm'));

      $this->add($this->component()->html($confirm))
        ->add($this->component()->html($this->view->translate('or')));

    }

    $cancel = $this->dom()->new_('a', array(
      'data-role' => 'button',
      'href' => $this->view->url(array('module' => 'credit', 'controller' => 'buy-offer', 'action' => 'pay', 'status' => 'cancel'), 'default', true)
    ), $this->view->translate('cancel'));

    $this->add($this->component()->html($cancel));

    $this->renderContent();
  }

  public function buyOfferPayAction()
  {
    /**
     * @var $order Offers_Model_Order
     * @var $subscription Offers_Model_Subscription
     */
    $subscriptionId = $this->_getParam('subscription_id', $this->offer_session->subscription_id);
    if (!$subscriptionId ||
      !($subscription = Engine_Api::_()->getItem('offers_subscription', $subscriptionId))) {
      return $this->_goBack();
    }

    $offer = $subscription->getOffer();
    $orderId = $this->_getParam('order_id', $this->offer_session->order_id);
    $order = Engine_Api::_()->getItem('offers_order', $orderId);

    if (!$order) {
      return $this->_goBack();
    }

    $credits = Engine_Api::_()->offers()->getCredits($offer->getPrice());
    $balances = Engine_Api::_()->getItem('credit_balance', $this->offer_user->getIdentity());

    if ($credits <= $balances->current_credit && $this->_getParam('status', '') == 'continue') {
      Engine_Api::_()->credit()->buyOffer($this->offer_user, (-1)*$credits, $offer->getIdentity());
      if ($offer->getPage()) {
        $owner = $offer->getOwner();
        $owner_balance = Engine_Api::_()->getItem('credit_balance', $owner->getIdentity());
        $owner_balance->setCredits($credits);
        $activity = Engine_Api::_()->getDbTable('actions', 'activity');
        $page = $this->offer_offer->getPage();
        if ($page) {
          $action = $activity->addActivity($this->offer_user, $page, 'page_offers_purchase', null, array('is_mobile' => true, 'link' => $this->offer_offer->getLink()));
          $activity->attachActivity($action, $this->offer_offer, Activity_Model_Action::ATTACH_DESCRIPTION);
          $activity->addActivity($this->offer_user, $this->_offer, 'offers_purchase', null, array('is_mobile' => true));
        } else {
          $activity->addActivity($this->offer_user, $this->offer_offer, 'offers_purchase', null, array('is_mobile' => true));
        }
      }
      $paymentStatus = 'okay';
    } else {
      $paymentStatus = 'pending';
    }

    $order->state = 'complete';
    $order->save();

    // Insert transaction
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'offers');
    $transactionsTable->insert(array(
      'user_id' => $order->user_id,
      'gateway_id' => 0,
      'timestamp' => new Zend_Db_Expr('NOW()'),
      'order_id' => $order->order_id,
      'type' => 'payment',
      'state' => $paymentStatus,
      'amount' => $offer->getPrice(), // @todo use this or gross (-fee)?
      'currency' => Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD')
    ));

    if ($paymentStatus == 'okay') {
      $subscription->onPaymentSuccess();

      // send notification
      if( $subscription->didStatusChange() ) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($this->offer_user, 'offers_subscription_active', array(
          'subscription_title' => $offer->title,
          'subscription_description' => $offer->description,
          'subscription_terms' => $offer->getOfferDescription('active'),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        ));
      }
      $status = 'active';
    } else {
      $subscription->onPaymentPending();

      // send notification
      if( $subscription->didStatusChange() ) {
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($this->offer_user, 'offers_subscription_pending', array(
          'subscription_title' => $offer->title,
          'subscription_description' => $offer->description,
          'subscription_terms' => $offer->getOfferDescription(),
          'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        ));
      }
      $status = 'pending';
    }

    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);
    unset($this->_session->errorMessage);

    return $this->redirect($this->view->url(array('action' => 'finish', 'state' => $status, 'offer_id' => $offer->offer_id), 'offers_subscription', true));
  }


  private function getUsersByText($text = null, $limit = 10)
  {
    /**
     * @var $table User_Model_DbTable_Users
     * @var $user User_Model_User
     **/

    $user = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbtable('users', 'user');
    $select = $user->membership()->getMembersOfSelect();
    $friends = $table->fetchAll($select);

    $ids = array(0);
    foreach( $friends as $friend ) {
      $ids[] = $friend->resource_id;
    }

    $select = $table->select()
      ->where("user_id IN(".join(',', $ids).")")
      ->group('user_id')
      ->limit($limit);

    if( $text ) {
      $select->where('displayname LIKE ?', $text);
    }

    $select1 = clone $select;

    if ($this->check($select1)) {
      return $table->fetchAll($select);
    }

    if( $text ) {
      $select->reset('where');
      $select
        ->where(sprintf("displayname LIKE %s OR displayname LIKE %s", "'".$text."'", "'".$text."%'"))
        ->where("user_id IN(".join(',', $ids).")")
      ;
    }

    $select2 = clone $select;

    if ($this->check($select2)) {
      return $table->fetchAll($select);
    }

    if( $text ) {
      $select->reset('where');
      $select
        ->where(sprintf("displayname LIKE %s OR displayname LIKE %s", "'".$text."'", "'%" . $text . "%'"))
        ->where("user_id IN(".join(',', $ids).")")
      ;
    }

    $select3 = clone $select;

    if ($this->check($select3)) {
      return $table->fetchAll($select);
    }

    return $table->fetchAll($select);
  }

  private function check($select)
  {
    /**
     * @var $table User_Model_DbTable_Users
     **/
    $table = Engine_Api::_()->getDbTable('users', 'user');
    if ($table->fetchRow($select)) {
      return true;
    } else {
      return false;
    }
  }

  private function getAllowTransfer($user_id)
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     */
    $table = Engine_Api::_()->getDbTable('logs', 'credit');
    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($user_id == $viewer->getIdentity()) {
      $max_send = $permissionsTable->getAllowed('credit', $viewer->level_id, 'max_send');
      if ($max_send === 0) {
        return true;
      } elseif ($max_send === null) {
        $max_send = 1500;
      }

      $select = $table->select()
        ->setIntegrityCheck(false)
        ->from(array('c' => $table->info('name')))
        ->joinLeft(array('a' => $actionTypes->info('name')), 'c.action_id = a.action_id')
        ->where('c.user_id = ?', $user_id)
        ->where('a.action_type = ?', 'transfer_to')
        ->where('c.creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL 1 DAY)"))
      ;

      $credits = $table->fetchAll($select);

      $all_credits = 0;
      foreach($credits as $credit) {
        $all_credits += (-1)*$credit->credit;
      }

      return ($all_credits < $max_send) ? true : false;
    } else {
      $recipient = Engine_Api::_()->getItem('user', $user_id);
      $max_receive = $permissionsTable->getAllowed('credit', $recipient->level_id, 'max_received');

      if ($max_receive === 0) {
        return true;
      } elseif ($max_receive === null) {
        $max_receive = 1500;
      }

      $select = $table->select()
        ->setIntegrityCheck(false)
        ->from(array('c' => $table->info('name')))
        ->joinLeft(array('a' => $actionTypes->info('name')), 'c.action_id = a.action_id')
        ->where('c.user_id = ?', $user_id)
        ->where('a.action_type = ?', 'transfer_from')
        ->where('c.creation_date > ?', new Zend_Db_Expr("DATE_SUB(NOW(), INTERVAL 1 DAY)"))
      ;

      $credits = $table->fetchAll($select);

      $all_credits = 0;
      foreach($credits as $credit) {
        $all_credits += $credit->credit;
      }

      return ($all_credits < $max_receive) ? true : false;
    }
  }
  //  } Index Controller

  protected function _goBack($back = true)
  {
    unset($this->offer_session->offer_id);
    unset($this->offer_session->subscription_id);
    unset($this->offer_session->gateway_id);
    unset($this->offer_session->order_id);
    unset($this->offer_session->errorMessage);

    if ($back) {
      return $this->redirect($this->view->url(array('action' => 'view', 'offer_id' => $this->offer_offer->getIdentity()), 'offers_specific', true));
    }
    return $this->redirect($this->view->url(array(), 'offers_upcoming', true));
  }

  protected function _checkOfferStatus(Zend_Db_Table_Row_Abstract $subscription = null)
  {
    if( !$this->offer_user ) {
      return false;
    }

    if (null === $subscription) {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'offers');
      $subscription = $subscriptionsTable->fetchRow(array(
        'user_id = ?' => $this->offer_user->getIdentity(),
        'offer_id = ?' => $this->offer_offer->getIdentity(),
        'active = ?' => true,
      ));
    }

    if (!$subscription) {
      return false;
    }

    if ($subscription->status == 'active' || $subscription->status == 'trial') {
      return true;
    } else if ($subscription->status == 'pending') {
      return true;
    }

    return false;
  }
    /**
     * Store Controller
     */
    public function storeInit()
    {
      $this->addPageInfo('contentTheme', 'd');
        //      $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        //        ->getNavigation('credit_main');

        //      if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {
        //        $this->_redirectCustom($this->view->url(array(), 'credit_general', true));
        //      }
    }

    public function storeIndexAction()
    {
        $order_ukey = $this->_getParam('vendor_order_id', '');
        $return_url = $this->_getParam('return_url', '');
        $cancel_url = $this->_getParam('cancel_url', '');

        if (!$cancel_url || !$return_url || !$order_ukey || !Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('store')) {
            $this->redirect($this->view->url(array(), 'credit_general', true));
        }

        /**
         * @var $order Store_Model_Order
         */
        $viewer = Engine_Api::_()->user()->getViewer();
        $order = Engine_Api::_()->getDbTable('orders', 'store')->getOrderByUkey($order_ukey);
        if ($order == null || $order->getUser() == null || $order->getUser()->getIdentity() != $viewer->getIdentity() || $order->status != 'initial') {
            $this->redirect($return_url . '&status=failed');
        }

        $orderItems = Zend_Paginator::factory($order->getItems());

        if (!$orderItems->count()) {
            $this->view->status = 'failed';
            return;
        }

        // Shipping Details
        $api = Engine_Api::_()->store();
        $detailsTbl = Engine_Api::_()->getDbTable('details', 'store');
        $locationsTbl = Engine_Api::_()->getDbTable('locations', 'store');
        $details = $detailsTbl->getDetails($viewer);
        $country = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_1']));
        $region = $locationsTbl->fetchRow(array('location_id = ?' => (int)$details['location_id_2']));
        $balance = Engine_Api::_()->getItem('credit_balance', $viewer->getIdentity())->current_credit;

        $prices = array(
            'items' => $api->getCredits($order->item_amt),
            'tax' => $api->getCredits($order->tax_amt),
            'shipping' => $api->getCredits($order->shipping_amt),
            'total' => $api->getCredits($order->item_amt + $order->tax_amt + $order->shipping_amt)
        );

        $tmp = array(
            'details'    => $details,
            'balance'    => $balance,
            'country'    => $country,
            'region'     => $region,
            'prices'     => $prices,
            'cancel_url' => $cancel_url
        );
        $page_params = array(
            'pay_url'       => $this->view->url(array('module' => 'credit', 'controller' => 'store', 'action' => 'pay'), 'default', true),
            'return_url'    => $return_url,
            'order_ukey'    => $order_ukey
        );
        $this->addPageInfo('pay_params', $page_params);
        $this
            ->add($this->component()->navigation('store_main', true))
            ->add($this->component()->creditCheckout($tmp))
            ->add($this->component()->itemList($orderItems, 'cartProductList', array('listPaginator' => true,)))
//            ->add($this->component()->paginator($orderItems))
            ->renderContent();
    }

    public function cartProductList(Core_Model_Item_Abstract $item)
    {
        $product = $item->getProduct();

        if($product->type == 'simple') {
            $quantity = "<div class='store_products_count'>".$this->view->translate('STORE_Quantity') . ': ' . $item->qty."</div>";
        } else {
            $quantity = '';
        }

        $customize_fields = array(
            'title' => $product->getTitle(),
            'creation_date' => $this->view->getPrice($product) . $quantity,
            'photo' => $product->getPhotoUrl('thumb.normal'),
            'manage' => ''
        );

        return $customize_fields;
    }

    public function storePayAction()
    {
        /**
         * @var $order Store_Model_Order
         * @var $api_credit Credit_Api_Core
         * @var $api_store Store_Api_Core
         */
        $order_ukey = $this->_getParam('ukey', '');
        if ($order_ukey == '') {
            $this->view->status = 0;
            $this->view->message = $this->view->translate('Invalid data');
        }

        $order = Engine_Api::_()->getDbTable('orders', 'store')->getOrderByUkey($order_ukey);
        if ($order == null) {
            $this->view->status = 0;
            $this->view->message = $this->view->translate('Invalid data');
            return;
        }

        $api_store = Engine_Api::_()->store();
        $api_credit = Engine_Api::_()->credit();
        $totalCredits = $api_store->getCredits($order->item_amt + $order->tax_amt + $order->shipping_amt);

        $buyer = $order->getUser();

        if ($buyer == null) {
            $this->view->status = 0;
            $this->view->message = $this->view->translate('Invalid data');
            return;
        }

        $buyerBalance = Engine_Api::_()->getItem('credit_balance', $buyer->getIdentity());

        if ($totalCredits > $buyerBalance->current_credit) {
            $this->view->status = 0;
            $this->view->message = $this->view->translate('You do not have enough credits to buy products');
            return;
        }

        $confirm_id = $api_credit->buyProducts($buyer, $order_ukey, (-1) * $totalCredits);
        $this->view->data = array('status' => 'completed', 'ukey' => $order_ukey, 'confirm_id' => $confirm_id);
        $this->view->status = 1;
    }

    /**
     * Store Controller
     */


    /**
     * Transaction Controller
     */
    /**
     * @var Credit_Model_Order
     */
    protected $_co;


    public function transactionInit()
    {
      $this->addPageInfo('contentTheme', 'd');

        // Get user and session
        $this->_user = Engine_Api::_()->user()->getViewer();
        $this->_session = new Zend_Session_Namespace('Credit_Transaction');

        // Check viewer and user
        if (!$this->_user || !$this->_user->getIdentity()) {
            if ($this->_session->__isset('user_id')) {
                $this->_user = Engine_Api::_()->getItem('user', $this->_session->user_id);
            }

            // If no user, redirect to home?
            if (!$this->_user || !$this->_user->getIdentity()) {
                $this->_redirector();
            }
        }
        $this->_session->user_id = $this->_user->getIdentity();

        // Get Credit order
        $co_id = $this->_getParam('co_id', $this->_session->co_id);

        if ($co_id) {
            $this->_co = Engine_Api::_()->getItem('credit_order', $co_id);
        } else {
            $this->_redirector();
        }

        // If no product or product is empty, redirect to home?
        if (!$this->_co || !$this->_co->getIdentity()) {
            $this->_redirector();
        }
        $this->_session->__set('co_id', $this->_co->getIdentity());
    }

    public function transactionIndexAction()
    {
        $this->redirect($this->view->url(array('action' => 'process'), 'credit_transaction', true));
    }

    public function transactionProcessAction()
    {
        /**
         * @var $gatewayTable Payment_Model_DbTable_Gateways
         * @var $gateway Payment_Model_Gateway
         * @var $api Credit_Api_Core
         */
        $api = Engine_Api::_()->credit();
        $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable->select()
            ->where('enabled = ?', 1)
            ->where('gateway_id = ?', $this->_co->gateway_id);

        if (null == ($gateway = $gatewayTable->fetchRow($gatewaySelect))) {
            $this->_redirector();
        }

        // Prepare host info
        $schema = 'http://';
        if (!empty($_ENV["HTTPS"]) && 'on' == strtolower($_ENV["HTTPS"])) {
            $schema = 'https://';
        }
        $host = $_SERVER['HTTP_HOST'];

        // Prepare transaction
        $params = array();
        $params['language'] = $this->_user->language;
        $localeParts = explode('_', $this->_user->language);
        if (count($localeParts) > 1) {
            $params['region'] = $localeParts[1];
        }
        $params['credit_order_id'] = $this->_co->getIdentity();
        $params['return_url'] = $schema . $host
            . $this->view->url(array('action' => 'return'))
            . '?order_id=' . $params['credit_order_id']
            . '&state=' . 'return';
        $params['cancel_url'] = $schema . $host
            . $this->view->url(array('action' => 'return'))
            . '?order_id=' . $params['credit_order_id']
            . '&state=' . 'cancel';
        $params['ipn_url'] = $schema . $host
            . $this->view->url(array('action' => 'index', 'controller' => 'ipn'))
            . '?order_id=' . $params['credit_order_id']
            . '&state=' . 'ipn';


        // Get gateway plugin
        /**
         * @var $plugin Credit_Plugin_Gateway_PayPal
         */

        $gatewayPlugin = $api->getGateway($this->_co->gateway_id);
        $plugin = $api->getPlugin($this->_co->gateway_id);
        $transaction = $plugin->createCreditTransaction($this->_co, $params);

        // Pull transaction params
        $transactionUrl = $gatewayPlugin->getGatewayUrl();
        $transactionMethod = $gatewayPlugin->getGatewayMethod();
        $transactionData = $transaction->getData();

        $form = new Engine_Form(
            array(
                'name' => 'transaction_form',
                'id' => 'transaction_form',
                'method' => 'post',
                'action' => $transactionUrl)
        );

        $order = 0;
        foreach ($transactionData as $key => $value) {
            $form->addElement('hidden',
                $key,
                array(
                    'value' => $value,
                    'order' => $order--
                )
            );
        }

        $this->add($this->component()->form($form));

        $this->_session->lock();

        // Handle redirection
        if ($transactionMethod == 'GET') {
            $transactionUrl .= '?' . http_build_query($transactionData);
            return $this->redirect($transactionUrl, array('prependBase' => false));
        }
        $this->renderContent();
        // Post will be handled by the view script
    }

    public function transactionReturnAction()
    {
        // Get order
        if (!$this->_user ||
            !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
            !($order = Engine_Api::_()->getItem('credit_order', $orderId)) ||
            !($gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id))
        ) {
            return $this->_finishPayment('failed');
        }

        /**
         * @var $api Credit_Api_Core
         * @var $plugin Credit_Plugin_Gateway_PayPal | Credit_Plugin_Gateway_2Checkout
         */

        $api = Engine_Api::_()->credit();
        $plugin = $api->getPlugin($gateway->getIdentity());

        try {
            $status = $plugin->onCreditTransactionReturn($this->_co, $this->_getAllParams());
        } catch (Payment_Model_Exception $e) {
            $status = 'failed';
            $this->_session->__set('errorMessage', $e->getMessage());
        }

        return $this->_finishPayment($status);
    }

    public function transactionFinishAction()
    {
        $this->view->status = $status = $this->_getParam('state');

        if ($status == 'completed') {
            $url = $this->view->escape($this->view->url(array('action' => 'manage'), 'credit_general', true));
        } else {
            $url = $this->view->escape($this->view->url((array()), 'credit_general', true));
            $this->view->error = $this->_session->errorMessage;
        }

        $this->view->continue_url = $url;

        $this->_session->unsetAll();
    }

    protected function _redirector()
    {
        $this->_session->unsetAll();
        $this->redirect($this->view->url(array(), 'credit_general', true));
    }
    /**
     * Transaction Controller
     */

}