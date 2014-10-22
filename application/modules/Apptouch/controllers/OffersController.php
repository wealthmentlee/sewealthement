<?php
  /**
   * SocialEngine
   *
   * @category   Application_Apptouch
   * @package    Apptouch
   * @copyright  Copyright Hire-Experts LLC
   * @license    http://www.hire-experts.com
   * @version    Id: OfferstController.php 15.11.12 12:21 Ulan T $
   * @author     Ulan T
   */

  /**
   * @category   Application_Extensions
   * @package    Apptouch
   * @copyright  Copyright Hire-Experts LLC
   * @license    http://www.hire-experts.com
   */

class Apptouch_OffersController extends Apptouch_Controller_Action_Bridge
{
  protected $viewer = null;
  protected $subject = null;
  protected $offer_id = 0;

  /**
   * @var User_Model_User
   */
  protected $_user;

  /**
   * @var Zend_Session_Namespace
   */
  protected $_session;

  /**
   * @var Offers_Model_Order
   */
  protected $_order;

  /**
   * @var Payment_Model_Gateway
   */
  protected $_gateway;

  /**
   * @var Offers_Model_Subscription
   */
  protected $_subscription;

  /**
   * @var Offers_Model_Offer
   */
  protected $_offer;

  public function indexBrowseAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();

    $viewer = Engine_Api::_()->user()->getViewer();
    $user_id = $viewer->getIdentity();
    $params['user_id'] = $user_id;

    if (!isset($params['filter']) || $params['filter'] != 'mine' && $params['filter'] != 'past') {
      $params['filter'] = 'upcoming';
    }
    if ($params['filter'] == 'mine') {
      if (!isset($params['my_offers_filter'])) {
        $params['my_offers_filter'] = 'upcoming';
      }
    }
    if ($this->_getParam('search', false)) {
      $params['searchText'] = $this->_getParam('search');
    }

    $params['page_num'] = $this->_getParam('page', 1);
    $this->isSuggestEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('suggest');

    $currentDate = date('Y-m-d h:i:s');
    $filter = $params['filter'];
    $my_offers_filter = isset($params['my_offers_filter']) ? $params['my_offers_filter'] : false;
    $paginator = Engine_Api::_()->getDbTable('offers', 'offers')->getOffersPaginator($params);

    $offerTbl = Engine_Api::_()->getDbTable('subscriptions','offers');
    foreach ($paginator as $offer) {
      if ($offer->time_limit == 'limit' && $my_offers_filter == 'past' && $currentDate > $offer->endtime) {
        $offerTbl->update(array('status' => 'expired'), array('offer_id = ?' => $offer->offer_id));
      }
    }
    $paginator = Engine_Api::_()->getDbTable('offers', 'offers')->getOffersPaginator($params);

    $form = $this->getSearchForm();
    $form->setMethod('get');
    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->addPageInfo('contentTheme', 'd');

    $this
      ->add($this->component()->itemSearch($form));
    $func = 'browseOffersList';

    if( $filter == 'mine' ) {
      $func = 'manageOffersList';

      $button_u = $this->dom()->new_('a', array(
        'href' => $this->view->url(array('my_offers_filter' => 'upcoming'), 'offers_mine'),
        'data-role' => 'button'
      ), $this->view->translate('Upcoming'));

      $button_p = $this->dom()->new_('a', array(
        'href' => $this->view->url(array('my_offers_filter' => 'past'), 'offers_mine'),
        'data-role' => 'button'
      ), $this->view->translate('Past'));

      $group_button = $this->dom()->new_('div', array('data-role' => 'controlgroup', 'data-type' => 'horizontal'));
      $group_button->append($button_u);
      $group_button->append($button_p);

      $this->add($this->component()->html($group_button));
    }

    $this
      ->add($this->component()->itemList($paginator, $func, array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->add($this->component()->navigation('offers_main', true), -1)
      ->renderContent();
  }

  public function indexChangeStatusCouponAction()
  {
    $offer_id = $this->_getParam('offer_id');
    $user_id = $this->_getParam('user_id', 0);
    $offerTbl = Engine_Api::_()->getDbTable('subscriptions', 'offers');

    $new_status = $offerTbl->changeStatusCoupon($offer_id, $user_id);

    $offer = Engine_Api::_()->getItem('offer', $offer_id);
    if($user_id)
      $this->redirect($offer->getHref() . '/tab/subscribers');
    else
      $this->redirect('parentRefresh');
  }


  public function offerInit()
  {
    $this->offer_id = $this->_getParam('offer_id', 0);
    $this->viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject()) {
      if ($this->offer_id && is_numeric($this->offer_id) && $this->offer_id > 0) {
        $this->subject = Engine_Api::_()->getItem('offer', $this->offer_id);
        if ($this->subject) {
          Engine_Api::_()->core()->setSubject($this->subject);
        }
      } else {
        return $this->redirect($this->view->url(array(), 'offers_upcoming', true));
      }
    }

    if (!$this->subject) {
      return $this->redirect($this->view->url(array(), 'offers_upcoming', true));
    }
  }

  public function offerViewAction()
  {
    if (!$this->subject->isEnable()) {
      if (!$this->subject->isOwner()) {
        return $this->redirect($this->view->url(array(), 'offers_upcoming', true));
      }
    }

    $this->setFormat('profile');
    $this->add($this->component()->quickLinks('offer_profile_quick', true))
      ->add($this->component()->navigation('offers_main', true))
    ->renderContent();
  }

  public function offerDeleteAction()
  {
    $this->subject = Engine_Api::_()->getItem('offer', $this->offer_id);
    if (!$this->_helper->requireAuth()->setAuthParams($this->subject, null, 'delete')->isValid())
      return $this->redirect($this->subject->getHref());

    $form = new Offers_Form_Delete();

    if (!$this->subject) {
      $this->view->status = false;
      $this->view->message = $this->view->translate('OFFERS_offer_no_to_delete');
      return $this->redirect($this->view->url(array(), 'offers_upcoming', true));
    }

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $db = $this->subject->getTable()->getAdapter();
    $db->beginTransaction();

    try {
      $this->subject->delete();
      $db->commit();
    } catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->view->status = true;
    $this->view->message = $this->view->translate("OFFERS_offer_deleted");

    return $this->redirect($this->view->url(array(), 'offers_upcoming', true));
  }

  public function offerFavoriteAction()
  {
    $offer_id = $this->_getParam('offer_id');
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $favoriteOffer_id = $settings->__get('offers.favorite.offer');

    $favoriteStatus = 'non_active';
    if ($favoriteOffer_id == $offer_id) {
      $favoriteStatus = 'active';
    }
    $form = new Offers_Form_Favorite($favoriteStatus);

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if ($favoriteStatus == 'non_active') {
      $settings->__set('offers.favorite.offer', $offer_id);
      $message = 'OFFERS_FAVORITE_The offer has been made as favorite successfully';
    }
    else {
      $settings->__set('offers.favorite.offer', 0);
      $message = 'OFFERS_FAVORITE_The offer has been made as simple successfully';
    }

    $this->view->message = $this->view->translate($message);
    return $this->redirect('parentRefresh');
  }

  public function offerEmailAction()
  {
    $offer_id = $this->subject->offer_id;
    $form = new Offers_Form_Email();

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


    $emailContent = $this->view->getEmailContent($offer_id);
    $remove = array("\n", "\r\n", "\r");
    $emailContent = str_replace($remove, ' ', $emailContent);

    $validateEmail = new Zend_Validate_EmailAddress();
    $emails = $this->_getParam('email_address');
    $viewer = $this->viewer;
    $viewer_id = $viewer->getIdentity();

    $emails = explode(',',$emails);
    $i = 0;
    foreach($emails as $email) {
      $emails[$i] = trim($email);
      $i++;
    }

    $senderName = '';
    $senderEmail = '';

    if ($viewer_id != 0) {
      $senderName = $viewer['displayname'];
      $senderEmail = $viewer['email'];
    }

    foreach ($emails as $email) {
      if (!$validateEmail->isValid($email)) {
        $form->addError($this->view->translate("OFFERS_%s is not valid email address, please correct and try again.", $email));
        $this->add($this->component()->form($form))
          ->renderContent();
        return;
      }

      $mail_settings = array(
        'date' => time(),
        'email_content' => $emailContent,
        'recipient_email' => $email,
        'sender_name' => $senderName,
        'sender_email' => $senderEmail
      );

      // send email
      Engine_Api::_()->getApi('mail', 'core')->sendSystemRaw(
        $email,
        'offers_email_template',
        $mail_settings
      );
    }

    $this->view->message = $this->view->translate('OFFERS_EMAIL_The offer has been sent successfully');
    return $this->redirect('parentRefresh');
  }


  public function profileInit()
  {
    $page_enabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');

    if (!$page_enabled) {
      return $this->redirect('parentRefresh');
    }

    $page_id = $this->_getParam('page_id', 0);
    $subject = ($page_id) ? Engine_Api::_()->getItem('page', $page_id) : null;

    if ($subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($subject)) {
      $subject = null;
    }

    $this->subject = $subject;
    if ($subject) {
      Engine_Api::_()->core()->setSubject($subject);
    }
    $viewer = Engine_Api::_()->user()->getViewer();
    $this->viewer = ($viewer) ? $viewer : 0;
  }

  public function profileIndexAction()
  {
    $filter = 'upcoming';

    $params = array(
      'page_id' => $this->subject->getIdentity(),
      'filter' => $filter,
      'user_id' => $this->viewer->getIdentity(),
      'page_num' => $this->_getParam('page', 1)
    );

    if ($this->_getParam('search', false)) {
      $params['searchText'] = $this->_getParam('search');
    }

    $table = Engine_Api::_()->getDbTable('offers', 'offers');

    $paginator = $table->getOffersPaginator($params);

    $form = $this->getSearchForm();
    $form->setMethod('get');
    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->add($this->component()->subjectPhoto($this->subject))
      ->add($this->component()->itemSearch($form))
      ->add($this->component()->itemList($paginator, 'browseOffersList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->add($this->component()->navigation('offer_profile_page', true))
      ->renderContent();
  }

  public function profilePastAction()
  {
    $filter = 'past';

    $params = array(
      'page_id' => $this->subject->getIdentity(),
      'filter' => $filter,
      'user_id' => $this->viewer->getIdentity(),
      'page_num' => $this->_getParam('page', 1)
    );

    if ($this->_getParam('search', false)) {
      $params['searchText'] = $this->_getParam('search');
    }

    $table = Engine_Api::_()->getDbTable('offers', 'offers');

    $paginator = $table->getOffersPaginator($params);

    $form = $this->getSearchForm();
    $form->setMethod('get');
    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->add($this->component()->subjectPhoto($this->subject))
      ->add($this->component()->itemSearch($form))
      ->add($this->component()->itemList($paginator, 'browseOffersList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->add($this->component()->navigation('offer_profile_page', true))
      ->renderContent();
  }

  public function profileManageAction()
  {
    $filter = 'manage';

    $params = array(
      'page_id' => $this->subject->getIdentity(),
      'filter' => $filter,
      'user_id' => $this->viewer->getIdentity(),
      'page_num' => $this->_getParam('page', 1)
    );

    if ($this->_getParam('search', false)) {
      $params['searchText'] = $this->_getParam('search');
    }

    $table = Engine_Api::_()->getDbTable('offers', 'offers');

    $paginator = $table->getOffersPaginator($params);

    $form = $this->getSearchForm();
    $form->setMethod('get');
    $form->getElement('search')->setValue($this->_getParam('search'));

    $this->add($this->component()->subjectPhoto($this->subject))
      ->add($this->component()->itemSearch($form))
      ->add($this->component()->itemList($paginator, 'browseOffersList', array('listPaginator' => true,)))
//      ->add($this->component()->paginator($paginator))
      ->add($this->component()->navigation('offer_profile_page', true))
      ->renderContent();
  }

  public function subscriptionInit()
  {
    $this->_session = new Zend_Session_Namespace('Offer_Subscription');
    // Get offer
    $offerId = $this->_getParam('offer_id', $this->_session->offer_id);

    if (!$offerId || !($this->_offer = Engine_Api::_()->getItem('offer', $offerId))) {
      $this->_goBack(false);
      return;
    }

    if (!($this->_offer->getCouponsCount() || $this->_offer->coupons_unlimit)) {
      $this->_goBack();
      return;
    }

    // Get user and session
    $this->_user = Engine_Api::_()->user()->getViewer();

    if ($this->_offer->isSubscribed($this->_user)) {
      $this->_goBack();
      return;
    }

    if ($this->_offer->getOfferType() == 'reward' || $this->_offer->getOfferType() == 'store') {
      $requires = $this->_offer->getRequire();
      $require_complete = Engine_Api::_()->getDbTable('require', 'offers')->getCompleteRequireIds($this->_user, $this->_offer);
      $requireIsComplete = true;
      foreach ($requires as $item) {
        if (!in_array($item->getIdentity(), $require_complete)) {
          $requireIsComplete = false;
          break;
        }
      }
      if (!$requireIsComplete) {
        $this->_goBack();
        return;
      }
    }

    // If there are no enabled gateways, disable
    if (!Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() > 0
      && !$this->_offer->isOfferCredit()
      && $this->_offer->getPrice()
    ) {
      $this->_goBack();
      return;
    }

    // Check viewer and user
    if (!$this->_user || !$this->_user->getIdentity()) {
      if (!empty($this->_session->user_id)) {
        $this->_user = Engine_Api::_()->getItem('user', $this->_session->user_id);
      }
      // If no user, redirect to home?
      if (!$this->_user || !$this->_user->getIdentity()) {
        $this->_session->unsetAll();
        return $this->_goBack();
      }
    }
  }

  public function subscriptionChooseAction()
  {
    // Check subscription status
    if ($status = $this->_checkOfferStatus()) {
      return $this->_finishPayment($status);
    }

    // Unset certain keys
    unset($this->_session->offer_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);
    unset($this->_session->errorMessage);

    // Process
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'offers');
    $user = $this->_user;

    // Insert the new temporary subscription
    $db = $subscriptionsTable->getAdapter();
    $db->beginTransaction();

    try {
      $values = array(
        'offer_id' => $this->_offer->offer_id,
        'user_id' => $user->getIdentity(),
        'status' => 'initial',
        'active' => false, // Will set to active on payment success
        'creation_date' => new Zend_Db_Expr('NOW()'),
      );
      if ($this->_offer->enable_unique_code) {
        $values['coupon_code'] = Engine_Api::_()->offers()->generateCouponsCode();
      }

      $subscription = $subscriptionsTable->createRow();
      $subscription->setFromArray($values);
      $subscription->save();

      // If the offer is free, let's set it active now
      if (!$this->_offer->getPrice()) {
        $subscription->setActive(true);
        $subscription->onPaymentSuccess();
        $activity = Engine_Api::_()->getDbTable('actions', 'activity');
        $page = $this->_offer->getPage();
        if ($page) {
          $action = $activity->addActivity($this->_user, $page, 'page_offers_accept', null, array('is_mobile' => true, 'link' => $this->_offer->getLink()));
          $activity->attachActivity($action, $this->_offer, Activity_Model_Action::ATTACH_DESCRIPTION);
          $activity->addActivity($this->_user, $this->_offer, 'offers_accept', null, array('is_mobile' => true));
        } else {
          $activity->addActivity($this->_user, $this->_offer, 'offers_accept', null, array('is_mobile' => true));
        }
      }

      Engine_Api::_()->getApi('mail', 'core')->sendSystemRaw($user, 'offers_subscription_active', array(
        'subscription_title' => $this->_offer->title,
        'subscription_description' => $this->_offer->description,
        'subscription_terms' => $this->_offer->getOfferDescription('active'),
        'object_link' => 'http://' . $_SERVER['HTTP_HOST'] .
          Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
      ));

      $subscription_id = $subscription->subscription_id;

      $db->commit();
    } catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }

    $this->_session->subscription_id = $subscription_id;

    // Otherwise redirect to the payment page
    return $this->redirect($this->view->url(array('action' => 'gateway', 'offer_id' => $this->_offer->offer_id), 'offers_subscription', true));
  }

  public function subscriptionFinishAction()
  {
    $status = $this->_getParam('state');
    $error = $this->_session->errorMessage;

    $form = new Engine_Form();
    $form->setAction($this->view->escape($this->view->url(array(), 'offers_mine', true)));

    if( $status == 'pending' ) {
      $form->setTitle('Payment Pending');
      $form->setDescription('Thank you for submitting your ' .
        'payment. Your payment is currently pending - your account ' .
        'will be activated when we are notified that the payment has ' .
        'completed successfully. Please return to our login page ' .
        'when you receive an email notifying you that the payment ' .
        'has completed.');
      $form->addElement('Button', 'submit', array(
        'label' => 'Back to Home',
        'type' => 'submit'
      ));
    } elseif($status == 'active') {
      $form->setTitle('Payment Complete');
      $form->setDescription('Thank you! Your payment has ' .
        'completed successfully.');
      $form->addElement('Button', 'submit', array(
        'label' => 'Continue',
        'type' => 'submit'
      ));
    } elseif( $status == 'accept' ) {
      $form->setTitle('Payment Complete');
      $form->setDescription('You have accepted offer successfully.');
      $form->addElement('Button', 'submit', array(
        'label' => 'Continue',
        'type' => 'submit'
      ));
    } else {
      $form->setTitle('Payment Failed');
      if( empty($error) ){
        $form->setDescription('Our payment processor has notified ' .
          'us that your payment could not be completed successfully. ' .
          'We suggest that you try again with another credit card ' .
          'or funding source.');
      } else {
        $form->setDescription($error);
      }

      $form->addElement('Button', 'submit', array(
        'label' => 'Back to Home',
        'type' => 'submit'
      ));
    }

    $this->add($this->component()->form($form))
      ->renderContent();
  }

  public function subscriptionGatewayAction()
  {
    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if( !$subscriptionId ||
      !($subscription = Engine_Api::_()->getItem('offers_subscription', $subscriptionId))  ) {
      $this->_goBack();
      return;
    }

    // Check subscription status
    if ($status = $this->_checkOfferStatus($subscription)) {
      return $this->_finishPayment($status);
    }

    // Get subscription
    if (!$this->_user ||
      $subscription->user_id != $this->_user->getIdentity() ||
      !($offer = Engine_Api::_()->getItem('offer', $subscription->offer_id))) {
      return $this->_goBack();
    }

    // Unset certain keys
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);

    $this->_session->offer_id = $offer->getIdentity();

    // Gateways
    if (null != ($page = $this->_offer->getPage())) {
      $gatewayTable = Engine_Api::_()->getDbtable('apis', 'offers');
      $gateways = $gatewayTable->getEnabledGateways($page->getIdentity());
    } else {
      $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
      $gatewaySelect = $gatewayTable->select()
        ->where('enabled = ?', 1);
      $gateways = $gatewayTable->fetchAll($gatewaySelect);
    }

    $gatewayPlugins = array();
    foreach( $gateways as $gateway ) {
      $gatewayPlugins[] = array(
        'gateway' => $gateway,
      );
    }

    $form = new Engine_Form();
    $form->setAction($this->view->escape($this->view->url(array('action' => 'process', 'offer_id' => $this->_offer->offer_id), 'offers_subscription', true)));
    $form->setTitle('OFFERS_Purchase Offer');
    $form->setDescription('OFFERS_Purchase Offer Gateways Description');

    foreach( $gatewayPlugins as $gatewayInfo ) {
      $gateway = $gatewayInfo['gateway'];
      $first = ( !isset($first) ? true : false );

      $form->addElement('Button', 'execute_' . $gateway->gateway_id, array(
        'type' => 'submit',
        'label' => $this->view->translate('Pay with %1$s', $this->view->translate($gateway->getTitle())),
        'prependText' => !$first ? ' or ' : '',
        'onclick' =>"$('#gateway_id').val(".$gateway->gateway_id.");"
      ));
    }

    if ($offer->isOfferCredit()) {
      $href = $this->view->url(array('module' => 'credit', 'controller' => 'buy-offer', 'action' => 'index', 'offer_id' => $offer->getIdentity()), 'default', true);
      $form->addElement('Button', 'execute', array(
        'label' => $this->view->translate('Pay with %1$s', $this->view->translate('Credits')),
        'onclick' => "window.location.href = '" . $href . "';"
      ));
    }

    $form->addElement('Hidden', 'gateway_id');

    $this->add($this->component()->form($form))
      ->renderContent();
  }

  public function subscriptionProcessAction()
  {
    // Get gateway
    $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
    if (null == ($page = $this->_offer->getPage())) {
      if (!$gatewayId ||
        !($gateway = Engine_Api::_()->getItem('payment_gateway', $gatewayId)) ||
        !($gateway->enabled)) {
        return $this->redirect($this->view->url(array('action' => 'gateway', 'offer_id' => $this->_offer->offer_id), 'offers_subscription', true));
      }
    } else {
      if (!$gatewayId ||
        !($gateway = Engine_Api::_()->getDbTable('apis', 'offers')->getApi($page->getIdentity(), $gatewayId)) ||
        !($gateway->enabled)) {
        return $this->redirect($this->view->url(array('action' => 'gateway', 'offer_id' => $this->_offer->offer_id), 'offers_subscription', true));
      }
    }

    // Get subscription
    $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
    if (!$subscriptionId ||
      !($subscription = Engine_Api::_()->getItem('offers_subscription', $subscriptionId))) {
      return $this->_goBack();
    }

    // Get package
    $offer = $subscription->getOffer();

    // Check subscription?
    if ($status = $this->_checkOfferStatus($subscription) ) {
      return $this->_finishPayment($status);
    }

    // Process

    // Create order
    $ordersTable = Engine_Api::_()->getDbtable('orders', 'offers');
    if (!empty($this->_session->order_id)) {
      $previousOrder = $ordersTable->find($this->_session->order_id)->current();
      if ($previousOrder && $previousOrder->state == 'pending') {
        $previousOrder->state = 'incomplete';
        $previousOrder->save();
      }
    }
    $ordersTable->insert(array(
      'user_id' => $this->_user->getIdentity(),
      'gateway_id' => $gateway->gateway_id,
      'state' => 'pending',
      'creation_date' => new Zend_Db_Expr('NOW()'),
      'source_type' => 'offers_subscription',
      'source_id' => $subscription->subscription_id,
    ));
    $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

    // Unset certain keys
    unset($this->_session->offer_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);

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
    if( count($localeParts) > 1 ) {
      $params['region'] = $localeParts[1];
    }
    $params['vendor_order_id'] = $order_id;
    $params['return_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $order_id
      . '&state=' . 'return';
    $params['cancel_url'] = $schema . $host
      . $this->view->url(array('action' => 'return'))
      . '?order_id=' . $order_id
      . '&state=' . 'cancel';
    $params['ipn_url'] = $schema . $host
      . $this->view->url(array('action' => 'index', 'controller' => 'ipn', 'module' => 'offers'))
      . '?order_id=' . $order_id;

    // Process transaction
    if ($page) {
      $api = Engine_Api::_()->getDbTable('apis', 'offers')->getApi($page->getIdentity(), $gatewayId);
      $gatewayPlugin = $api->getGateway();
      $plugin = $api->getPlugin();
    } else {
      $api = Engine_Api::_()->offers();
      $gatewayPlugin = $api->getGateway($gateway->gateway_id);
      $plugin = $api->getPlugin($gateway->gateway_id);
    }

    $transaction = $plugin->createOfferTransaction($this->_user, $subscription, $offer, $params);

    // Pull transaction params

    $transactionUrl = $gatewayPlugin->getGatewayUrl();
    $transactionMethod = $gatewayPlugin->getGatewayMethod();
    $transactionData = $transaction->getData();

    $form = new Engine_Form();
    $form->setTitle('Loading...');
    $form->setAction($transactionUrl);
    $form->setMethod($transactionMethod);

    $order = 0;
    foreach( $transactionData as $key => $data ) {
      $form->addElement('Hidden', $key, array(
        'value' => $data,
        'order' => $order
      ));
      $order++;
    }

    $this->add($this->component()->form($form))
      ->renderContent();
    // Post will be handled by the view script
  }

  public function subscriptionReturnAction()
  {
    // Get order
    if (!$this->_user ||
      !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
      !($order = Engine_Api::_()->getItem('offers_order', $orderId)) ||
      $order->source_type != 'offers_subscription' ||
      !($subscription = $order->getSource()) ||
      !($offer = $subscription->getOffer())) {
      return $this->_goBack();
    }

    if (null == ($page = $offer->getPage())) {
      if (!($gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id))) {
        return $this->_goBack();
      }
    } else {
      if (!($gateway = Engine_Api::_()->getDbTable('apis', 'offers')->getApi($page->getIdentity(), $order->gateway_id))) {
        $this->_goBack();
      }
    }

    if ($page) {
      $api = Engine_Api::_()->getDbTable('apis', 'offers')->getApi($page->getIdentity(), $gateway->gateway_id);
      $gatewayPlugin = $api->getGateway();
      $plugin = $api->getPlugin();
    } else {
      $api = Engine_Api::_()->offers();
      $gatewayPlugin = $api->getGateway($gateway->gateway_id);
      $plugin = $api->getPlugin($gateway->gateway_id);
    }

    // Process return
    unset($this->_session->errorMessage);
    try {
      $status = $plugin->onOfferTransactionReturn($order, $this->_getAllParams());
      if ($status == 'active') {
        $activity = Engine_Api::_()->getDbTable('actions', 'activity');
        $page = $this->_offer->getPage();
        if ($page) {
          $action = $activity->addActivity($this->_user, $page, 'page_offers_purchase', null, array('is_mobile' => true, 'link' => $this->_offer->getLink()));
          $activity->attachActivity($action, $this->_offer, Activity_Model_Action::ATTACH_DESCRIPTION);
          $activity->addActivity($this->_user, $this->_offer, 'offers_purchase', null, array('is_mobile' => true));
        } else {
          $activity->addActivity($this->_user, $this->_offer, 'offers_purchase', null, array('is_mobile' => true));
        }
      }
    } catch( Payment_Model_Exception $e ) {
      $status = 'failure';
      $this->_session->errorMessage = $e->getMessage();
    }

    return $this->_finishPayment($status);
  }


  public function tabDetails($active = false)
  {

    if( $active ) {
      if (!Engine_Api::_()->core()->hasSubject('offer')) {
        return false;
      }

      /**
       * @var $offer Offers_Model_Offer
       * @var $modules Hecore_Model_DbTable_Modules
       */
      $modules = Engine_Api::_()->getDbTable('modules', 'hecore');
      $viewer = Engine_Api::_()->user()->getViewer();

      $offer = Engine_Api::_()->core()->getSubject();

      $offerPhotos = $offer->getCollectiblesPaginator();
      $isSubscribed = $offer->isSubscribed($viewer);
      $page = null;

      if ($modules->isModuleEnabled('page')) {
        $page = Engine_Api::_()->getItem('page', $offer->page_id);
      }

      $products = $offer->getProductsStore($offer->getIdentity());
      $requireIsComplete = true;

      if ($offer->getOfferType() == 'reward' || $offer->getOfferType() == 'store') {
        $requires = $offer->getRequire();
        $require_complete = array();
        if ($viewer->getIdentity()) {

          $require_complete = Engine_Api::_()->getDbTable('require', 'offers')->getCompleteRequireIds($viewer, $offer, $offer->page_id);

          foreach ($requires as $item) {
            if (!in_array($item->getIdentity(), $require_complete)) {
              $requireIsComplete = false;
              break;
            }
          }
        }
      }

      $checkTimeLeft = $offer->checkTime($offer->endtime);
      $checkTimeRedeem = $offer->checkTime($offer->redeem_endtime);
      $time_left = Engine_Api::_()->offers()->availableOffer($offer, true);
      $result_redeem = preg_match('/(0{4})-(0{2})-(0{2}) (0{2}):(0{2}):(0{2})/', $offer->redeem_starttime);
      $currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

      $values = array();
      $values[] = array(
        'title' => $this->view->translate('Offer Details'),
        'content' => array()
      );
      if( $offer->type != 'paid' ) {
        $values[0]['content'][] = array(
          'label' => $this->view->translate('OFFERS_offer_price'),
          'value' => $this->view->getOfferPrice($offer)
        );
      }
      if(isset($offer->price_item) && !empty($offer->price_item)) {
        $values[0]['content'][] = array(
          'label' => $this->view->translate('OFFERS_Item Price'),
          'value' => $this->view->locale()->toCurrency((double)$offer->price_item, $currency)
        );
      }
      $values[0]['content'][] = array(
        'label' => $this->view->translate('OFFERS_offer_discount'),
        'value' => $this->view->getOfferDiscount($offer)
      );

      if(!$offer->coupons_unlimit) {
        $values[0]['content'][] = array(
          'label' => $this->view->translate('OFFERS_offer_available'),
          'value' => ($offer->coupons_count > 0) ? (($offer->coupons_unlimit) ? $this->view->translate('unlimit coupons') : $this->view->translate('%s coupons', $offer->coupons_count)) : $this->view->translate('OFFERS_offer_not_left')
        );
      }

      if($time_left != 'Unlimited') {
        $values[0]['content'][] = array(
          'label' => $this->view->translate('OFFERS_offer_time_left'),
          'value' => ($checkTimeLeft) ? $time_left : $this->view->translate('OFFERS_offer_time_is_up')
        );
      }

      $values[0]['content'][] = array(
        'label' => $this->view->translate('OFFERS_offer_redeem'),
        'value' => (!$result_redeem) ? (($checkTimeRedeem) ? date('M d, Y', strtotime($offer->redeem_starttime)) . ' - ' . date('M d, Y', strtotime($offer->redeem_endtime)) : $this->view->translate('OFFERS_offer_no_relevant')) : $this->view->translate('OFFERS_Unlimit')
      );
      if (isset($page) && $page) {
        $values[0]['content'][] = array(
          'label' => $this->view->translate('OFFERS_offer_presented_by'),
          'value' => $this->view->htmlLink($page->getHref(), $page->getTitle())
        );
      }
      $counter = 1;
      $this->add(($this->component()->customComponent('fieldsValues', $values)), 10);
      $this->addPageInfo('fields', $values);

      if(!$offer->isEnable()) {
        $this->add($this->component()->html($this->view->translate('OFFERS_offer_disabled')), 11);
      }

      if( $viewer && $viewer->getIdentity() ) {
        if($checkTimeLeft ) {
          if (!$isSubscribed) {
            if ($offer->getPrice()) {
              $this->add($this->component()->html($this->view->getOfferPrice($offer)), 12);
            }
            if( $offer->coupons_unlimit == 1 || $offer->coupons_count > 0 ) {
              if ($offer->getOfferType() == 'free')
                $label = $this->view->translate('OFFERS_Accept Offer');
              elseif($offer->getOfferType() == 'reward' || $offer->getOfferType() == 'store')
                $label = $this->view->translate('OFFERS_Accept Offer');
              elseif($offer->getOfferType() == 'paid')
                $label = $this->view->translate('OFFERS_Purchase Offer');

              $btn = $this->dom()->new_('a', array(
                'data-role' => 'button',
                'disable' => true,
                'class' => $requireIsComplete ? '' : 'ui-disabled',
                'href' => $this->view->url(array('offer_id' => $offer->getIdentity()), 'offers_subscription', true)
              ), $label);

              $this->add($this->component()->html($btn), 13);

              if ($offer->getOfferType() == 'reward' || $offer->getOfferType() == 'store') {
                $this->add($this->component()->html('<b>' . $this->view->translate('As soon as you will fulfill the following requirements you will be able to get this offer') . '</b>'), 14);
              }
            }
            if( !empty($requires ) ) {
              foreach( $requires as $item ) {
                $require = Engine_Api::_()->offers()->getRequire($item->type, ($offer->getPage() ? 'page' : 0));
                if (empty($require)) {
                  continue ;
                }

                $link = 'javascript:void(0);';
                if (!empty($require['require_link'])) {
                  $link = $require['require_link'];
                }

                if($item->type == 'likepage')
                  $text = $this->view->translate('OFFERS_REQUIRE_' . strtoupper($item->type), $this->view->htmlLink($page->getHref(), $page->getTitle(), array()));
                elseif($item->type == 'review' && $offer->page_id)
                  $text = $this->view->translate('OFFERS_REQUIRE_PAGEREVIEW' , $this->view->htmlLink($page->getHref(), $page->getTitle(), array()));
                elseif($item->type == 'suggest' && $offer->page_id)
                  $text = $this->view->translate('OFFERS_REQUIRE_PAGESUGGEST' , $this->view->htmlLink($page->getHref(), $page->getTitle(), array()), $item->params['count']);
                else
                  $text = $this->view->translate('OFFERS_REQUIRE_' . strtoupper($item->type), $item->params);

                $this->add($this->component()->html($text), 14 + $counter);
                $counter++;
              }
            }
          } else {
            if ($offer->getOfferType() == 'paid') {
              $this->add($this->component()->html($this->view->translate('You have already purchased this offer')), 12);
            } else {
              $this->add($this->component()->html($this->view->translate('You have already accepted this offer')), 12);
            }
          }
        } else {
          $this->add($this->component()->html($this->view->translate('OFFERS_offer_time_left_expired')), 12);
        }
      }

      $this->add($this->component()->html($offer->description), 14 + $counter);
      if(($offer->type === 'store') && ($products) && (count($products) > 0)) {
        $this->add($this->component()->html('<br><b>' . $this->view->translate('OFFERS_offer_products') . '<br></b><br>'), 15 + $counter);
        $list = '<ul data-role="listview">';
        foreach( $products as $product ) {
          $list .= '<li data-iconshadow="true"><a href="' . $product->getHref() .'">' . '<img src="' . $product->getPhotoUrl() . '">' . '<h3>' . $product->getTitle() . '</h3>' . '<p>' . $this->view->locale()->toCurrency($product->price, $currency) . '</p>' .'</a></li>';
        }
        $list .= '</ul>';
        $this->add($this->component()->html($list . '<br>'), 16 + $counter);
      }

      if($offerPhotos->getTotalItemCount() > 0) {
        $this->add($this->component()->gallery($offerPhotos), 17 + $counter);
      }
    }

    return true;
  }

  public function tabReviews($active = false)
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate')) {
      return false;
    }

    if( $active ) {
      /**
       * @var $subject Offers_Model_Offer
       */
      $subject = Engine_Api::_()->core()->getSubject('offer');
      $viewer = Engine_Api::_()->user()->getViewer();

      if ( !($subject instanceof Offers_Model_Offer) ) {
        return false;
      }

      $this->view->headTranslate(array('RATE_REVIEW_DELETE', 'RATE_REVIEW_DELETEDESC'));

      $offer_id = $subject->getIdentity();

      $types = Engine_Api::_()->getApi('core', 'rate')->getOfferTypes($offer_id);
      $countOptions = count($types);

      $p = $this->_getParam('page', 1);

      $tbl = Engine_Api::_()->getDbTable('offerreviews', 'rate');
      $paginator = $tbl->getPaginator($offer_id, $viewer->getIdentity(), $p);

      $isAllowedPost = $tbl->isAllowedPost($offer_id, $viewer);

      $form = $this->getSearchForm();
      $form->setMethod('get');
      $form->getElement('search')->setValue($this->_getParam('search'));

      $this->add($this->component()->itemSearch($form), 9)
        ->add($this->component()->itemList($paginator, 'manageReviewList', array('listPaginator' => true,)), 10)
//        ->add($this->component()->paginator($paginator), 11)
      ;

      if( $isAllowedPost ) {
        $this->add($this->component()->navigation('offerreview_create', true), 12);
      }
    }

    return true;
  }

  public function tabSubscribers($active = false)
  {
    if (Engine_Api::_()->core()->hasSubject('offer')) {
      $offer = Engine_Api::_()->core()->getSubject();
    } else {
      return false;
    }

    if (!$offer->isOwner()) {
      return false;
    }

    if( $active ) {
      $subscribers = $offer->getSubscriptions();
      $data = array();
      if(count($subscribers)) {
        foreach($subscribers as $subscriber) {
          $element = $this->dom()->new_('div', array('data-role' => 'collapsible', 'data-content-theme' => 'd'));
          $title = $this->dom()->new_('h3', array(), $this->view->htmlLink($subscriber->getHref(), $subscriber->getTitle()));

          $text = '<table><tr><th>' . $this->view->translate('OFFERS_time_acquisition') . '</th><td> : ' . $subscriber->creation_date . '</td></tr>';
          $text .= '<tr><th>' . $this->view->translate('OFFERS_title_status_coupon') . '</th><td> : ' . $subscriber->status . '</td></tr>';
          $text .= '<tr><th>' . $this->view->translate('OFFERS_title_coupon_code') . '</th><td> : ' . $offer->getCouponCode($subscriber->getIdentity()) . '</td></tr>';
          $text .= '</table>';

          $href = $this->view->url(array('action' => 'change-status-coupon', 'offer_id' => $offer->getIdentity(), 'user_id' => $subscriber->getIdentity()), 'offers_general', true);
          $text .= '<a data-role="button" href="'. $href . '">' . (($subscriber->status == 'active') ? $this->view->translate('OFFERS_change_status_coupon', 'Used') : $this->view->translate('OFFERS_change_status_coupon', 'Active')) . '</a>';

          $content = $this->dom()->new_('p', array(), $text);
          $element->append($title);
          $element->append($content);
          $data[] = $element;
        }
        $this->add($this->component()->html($data), 20);
      }

      $this->add($this->component()->html('<b>'.$this->view->translate('OFFERS_total_subscribers') . '</b> ' . count($subscribers)), 21);
    }

    return true;
  }


  public function browseOffersList(Core_Model_Item_Abstract $item)
  {
    if( Engine_Api::_()->offers()->availableOffer($item, true) != 'Unlimit' ) {
      $desc = $this->view->translate('OFFERS_offer_time_left') .  Engine_Api::_()->offers()->availableOffer($item, true);
    } else {
      if(!$item->coupons_unlimit) {
        $desc = $this->view->translate('OFFERS_offer_available') . ' ' . $this->view->translate('%s coupons', $item->coupons_count);
      }
    }
    $discount = '<div style="color:red;">'. $this->view->translate('OFFERS_offer_discount') . '' .$this->view->getOfferDiscount($item). '</div>';
    $customize_fields = array(
      'descriptions' => array($desc, $discount),
      'creation_date' => ''
    );

    return $customize_fields;
  }

  public function manageOffersList(Core_Model_Item_Abstract $item)
  {
    $title = '<h3>' . $this->view->htmlLink($item->getHref(), $this->view->string()->truncate($item->getTitle(), 20)) . '</h3>';
    $text = '<b>'. $this->view->translate('OFFERS_offer_price') . '</b>  ' . $this->view->getOfferPrice($item) . '<br>';
    $text .= '<b>'. $this->view->translate('OFFERS_offer_discount') . '</b>  ' . $this->view->getOfferDiscount($item). '<br>';
    $text .= '<b>'. $this->view->translate('OFFERS_offer_available') . '</b>  ' . ((($item->coupons_unlimit) || ($item->coupons_count > 0)) ? (($item->coupons_unlimit) ? $this->view->translate('unlimit coupons') : $this->view->translate('%s coupons', $item->coupons_count)) : $this->view->translate('OFFERS_offer_not_left')) . '<br>';
    $text .= '<b>'. $this->view->translate('OFFERS_Redeem') . '</b>  ' . Engine_Api::_()->offers()->timeInterval($item) . '<br>';
    if( $item->page_id ) {
      $text .= '<b>'. $this->view->translate('OFFERS_Presented by') . '</b>  ' . $item->page_title . '<br>';
    }

    $my_offers_filter = 'upcoming';
    if($this->getRequest()->getParam('my_offers_filter')) {
      $my_offers_filter = $this->getRequest()->getParam('my_offers_filter');
    }


    if( $item->status == 'expired' && $my_offers_filter == 'past' ) {
      $text .= '<b>'. $this->view->translate('OFFERS_Status') . '</b>  ' . $this->view->translate('OFFERS_Expired') . '<br>';
    }
    $text .= '<b>'. $this->view->translate('OFFERS_offer_coupon_code') . '</b>  ' . $item->getCouponCode() . '<br>';

    if($item->address != '' || $item->city != '' || $item->state != '' || $item->country != '') {
      $text .= '<b>';
      if($item->address != '')
        $text .= $item->address . ', ';
      if($item->city != '')
        $text .= $item->city . ', ';
      if($item->state != '')
        $text .= $item->state . ', ';
      if($item->country != '')
        $text .= $item->country;
      $text .= '</b><br>';
    }

    if($item->phone != '') {
      $text .= '<b>'. $item->phone . '</b><br>';
    }

    if($item->website != '') {
      $text .= '<b>'. $item->website . '</b><br>';
    }

    $body = '<p>' . $text . '</p>';
    $element = '<div data-role="collapsible" data-content-theme="c">' . $title . $body . '</div>';

    $options = array();

    if ($this->isSuggestEnabled && $my_offers_filter == 'upcoming') {
      $suggest_type = 'link_'.$item->getType();
      $view = Zend_Registry::get('Zend_View');

      $router = Zend_Controller_Front::getInstance()->getRouter();
      $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR ."modules" . DIRECTORY_SEPARATOR . "Mobile" . DIRECTORY_SEPARATOR .
        "modules" . DIRECTORY_SEPARATOR . "Suggest" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts";

      $paramStr = '?m=suggest&c=' . $view->url(array(
        "controller" => "index",
        "action" => "suggest",
        "object_id" => $item->getIdentity(),
        "object_type" => $item->getType(),
        "suggest_type" => $suggest_type
      ), "suggest_general") . '&l=getSuggestItems&nli=0&params[object_type]='.$item->getType().'&params[object_id]='.$item->getIdentity() .
        '&action_url='.urlencode($router->assemble(array('action' => 'suggest'), 'suggest_general')) .
        '&params[suggest_type]=' . $suggest_type . '&params[scriptpath]=' . $path;

      $url = $router->assemble(array('controller' => 'index', 'action' => 'contacts', 'module' => 'hecore'), 'default', true) . $paramStr;

      $options[] = array(
        'label' => $this->view->translate('Suggest To Friends'),
        'attrs' => array(
          'href' => $url,
          'class' => 'buttonlink',
          'data-rel' => 'dialog'
        )
      );
    }

    if ($my_offers_filter == 'upcoming' && $item->active && $item->status == 'active') {
      $options[] = array(
        'label' => $this->view->translate('OFFERS_change_status_coupon', 'Used'),
        'attrs' => array(
          'href' => $this->view->url(array('action' => 'change-status-coupon', 'offer_id' => $item->getIdentity()), 'offers_general'),
          'class' => 'buttonlink',
        )
      );
    }

    if ($item->status != 'expired' && $my_offers_filter != 'past') {
      $options[] = array(
        'label' => $this->view->translate('OFFERS_Email'),
        'attrs' => array(
          'href' => $this->view->url(array('action' => 'email', 'offer_id' => $item->offer_id), 'offers_specific'),
          'class' => 'buttonlink',
        )
      );

    }

    $customize_fields = array(
//      'counter' => $this->view->translate('OFFERS_offer_discount') . '' . $this->view->getOfferDiscount($item),
      'title' => '',
      'manage' => $options,
      'descriptions' => array($element),
      'creation_date' => ''
    );

    return $customize_fields;
  }

  //=------------------------------------------ Review Customizer Functions ---------------------------------
  public function manageReviewList(Core_Model_Item_Abstract $item)
  {
    $options = array();
    $offer_id = $item->getOffer()->getIdentity();
    $viewer = Engine_Api::_()->user()->getViewer();

    $isAllowedRemove = $viewer->isOwner($item->getOwner()) || Engine_Api::_()->getApi('core', 'rate')->isAllowRemoveReview($offer_id, $viewer);

    if ($item->isOwner($viewer)) {
      $options[] = array(
        'label' => $this->view->translate('Edit'),
        'attrs' => array(
          'href' => $this->view->url(array('action' => 'edit', 'offer_id' => $offer_id, 'review_id' => $item->getIdentity()), 'offer_review'),
          'class' => 'buttonlink'
        )
      );
    }

    if ($isAllowedRemove) {
      $options[] = array(
        'label' => $this->view->translate('Delete'),
        'attrs' => array(
          'href' => $this->view->url(array('action' => 'remove', 'offer_id' => $offer_id, 'review_id' => $item->getIdentity()), 'offer_review'),
          'class' => 'buttonlink',
        )
      );
    }

    if (isset($item->photo_id)) {
      $photoUrl = $item->getPhotoUrl('thumb.normal');
    } else {
      $photoUrl = $item->getOwner()->getPhotoUrl('thumb.normal');
    }

    $customize_fields = array(
      'title' => $item->getTitle(),
      'descriptions' => array(
        '<div class="small_rate_star">' . $this->view->reviewRate($item->rating, true) . '</div>'
      ),
      'photo' => $photoUrl,
      'manage' => $options,
      'counter' => round($item->rating, 2),
      'href' => $this->view->url(array('action' => 'view', 'review_id' => $item->offerreview_id), 'offer_review', true)
    );

    return $customize_fields;
  }

//=------------------------------------------ Review Customizer Functions ---------------------------------

  protected function _goBack($back = true)
  {
    unset($this->_session->offer_id);
    unset($this->_session->subscription_id);
    unset($this->_session->gateway_id);
    unset($this->_session->order_id);
    unset($this->_session->errorMessage);

    if ($back) {
      return $this->redirect($this->view->url(array('action' => 'view', 'offer_id' => $this->_offer->getIdentity()), 'offers_specific', true));
    }

    return $this->redirect($this->view->url(array(), 'offers_upcoming', true));
  }

  protected function _finishPayment($state = 'active')
  {
    // No user?
    if( !$this->_user ) {
      $this->_goBack();
      return;
    }

    // Clear session
    $errorMessage = $this->_session->errorMessage;
    $userIdentity = $this->_session->user_id;
    $this->_session->unsetAll();
    $this->_session->user_id = $userIdentity;
    $this->_session->errorMessage = $errorMessage;

    if ($state == 'active' && (in_array($this->_offer->getOfferType(), array('reward', 'free', 'store')))) {
      $state = 'accept';
    }

    // Redirect
    return $this->redirect($this->view->url(array('action' => 'finish', 'state' => $state, 'offer_id' => $this->_offer->offer_id), 'offers_subscription', true));
  }

  protected function _checkOfferStatus(Zend_Db_Table_Row_Abstract $subscription = null)
  {
    if( !$this->_user ) {
      return false;
    }

    if (null === $subscription) {
      $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'offers');
      $subscription = $subscriptionsTable->fetchRow(array(
        'user_id = ?' => $this->_user->getIdentity(),
        'offer_id = ?' => $this->_offer->getIdentity(),
        'active = ?' => true,
      ));
    }

    if (!$subscription) {
      return false;
    }

    if ($subscription->status == 'active' || $subscription->status == 'trial') {
      return 'active';
    } else if ($subscription->status == 'pending') {
      return 'pending';
    }

    return false;
  }
}