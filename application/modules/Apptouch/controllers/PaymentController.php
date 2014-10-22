<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 24.07.12
 * Time: 11:40
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_PaymentController extends Apptouch_Controller_Action_Bridge
{


    /**
     *   Subscription Controller
     */

    /**
     * @var User_Model_User
     */
    protected $_user;

    /**
     * @var Zend_Session_Namespace
     */
    protected $_session;

    /**
     * @var Payment_Model_Order
     */
    protected $_order;

    /**
     * @var Payment_Model_Gateway
     */
    protected $_gateway;

    /**
     * @var Payment_Model_Subscription
     */
    protected $_subscription;

    /**
     * @var Payment_Model_Package
     */
    protected $_package;
  public function settingsInit()
  {
    // Can specifiy custom id
    $id = $this->_getParam('id', null);
    $subject = null;
    if( null === $id ) {
      $subject = Engine_Api::_()->user()->getViewer();
      Engine_Api::_()->core()->setSubject($subject);
    } else {
      $subject = Engine_Api::_()->getItem('user', $id);
      Engine_Api::_()->core()->setSubject($subject);
    }

    // Set up require's
    $this->_helper->requireUser();
    $this->_helper->requireSubject();
    $this->_helper->requireAuth()->setAuthParams(
      $subject,
      null,
      'edit'
    );

    // Set up navigation
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'core')
      ->getNavigation('user_settings', ( $id ? array('params' => array('id'=>$id)) : array()));
  }

  public function settingsIndexAction()
  {
    $user = Engine_Api::_()->core()->getSubject('user');

    // Check if they are an admin or moderator (don't require subscriptions from them)
    $level = Engine_Api::_()->getItem('authorization_level', $user->level_id);
    if( in_array($level->type, array('admin', 'moderator')) ) {
      $this->view->isAdmin = true;
      return;
    }

    // Get packages
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $this->view->packages = $packages = $packagesTable->fetchAll(array('enabled = ?' => 1, 'after_signup = ?' => 1));

    // Get current subscription and package
    $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
    $this->view->currentSubscription = $currentSubscription = $subscriptionsTable->fetchRow(array(
      'user_id = ?' => $user->getIdentity(),
      'active = ?' => true,
    ));

    // Get current package
    if( $currentSubscription ) {
      $this->view->currentPackage = $currentPackage = $packagesTable->fetchRow(array(
        'package_id = ?' => $currentSubscription->package_id,
      ));
    }

    // Get current gateway?
  }

  public function settingsConfirmAction()
  {
    // Process
    $user = Engine_Api::_()->core()->getSubject('user');

    // Get packages
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $this->view->package = $package = $packagesTable->fetchRow(array(
      'enabled = ?' => 1,
      'package_id = ?' => (int) $this->_getParam('package_id'),
    ));

    // Check if it exists
    if( !$package ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

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
    if( $package->package_id == $currentPackage->package_id ) {
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
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
      return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    // Prepare subscription session
    $session = new Zend_Session_Namespace('Payment_Subscription');
    $session->is_change = true;
    $session->user_id = $user->getIdentity();
    $session->subscription_id = $subscription_id;

    // Redirect to subscription handler
    return $this->_helper->redirector->gotoRoute(array('controller' => 'subscription',
      'action' => 'gateway'));
  }

    public function subscriptionInit()
    {
        // If there are no enabled gateways or packages, disable
        if (Engine_Api::_()->getDbtable('gateways', 'payment')->getEnabledGatewayCount() <= 0 ||
            Engine_Api::_()->getDbtable('packages', 'payment')->getEnabledNonFreePackageCount() <= 0
        ) {
            return $this->_helper->redirector->gotoRoute(array(), 'default', true);
        }

        // Get user and session
        $this->_user = Engine_Api::_()->user()->getViewer();
        $this->_session = new Zend_Session_Namespace('Payment_Subscription');

        // Check viewer and user
        if (!$this->_user || !$this->_user->getIdentity()) {
            if (!empty($this->_session->user_id)) {
                $this->_user = Engine_Api::_()->getItem('user', $this->_session->user_id);
            }
            // If no user, redirect to home?
            if (!$this->_user || !$this->_user->getIdentity()) {
                $this->_session->unsetAll();
                return $this->_helper->redirector->gotoRoute(array(), 'default', true);
            }
        }
    }

    public function subscriptionIndexAction()
    {
        return $this->_forward('choose');
    }

    public function subscriptionChooseAction()
    {
        // Check subscription status
        //if( $this->_checkSubscriptionStatus() ) {
        //  return;
        //}

        // Unset certain keys
        unset($this->_session->package_id);
        unset($this->_session->subscription_id);
        unset($this->_session->gateway_id);
        unset($this->_session->order_id);
        unset($this->_session->errorMessage);

        // Check for default plan
        $this->_checkDefaultPaymentPlan();

        // Make form
        $this->view->form = $form = new Payment_Form_Signup_Subscription(array(
            'isSignup' => false,
            'action' => $this->view->url(),
        ));

        // Check method/valid
        if (!$this->getRequest()->isPost()) {
            return;
        }
        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Get package
        if (!($packageId = $this->_getParam('package_id', $this->_session->package_id)) ||
            !($package = Engine_Api::_()->getItem('payment_package', $packageId))
        ) {
            return;
        }
        $this->view->package = $package;


        // Process
        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
        $user = $this->_user;
        $currentSubscription = $subscriptionsTable->fetchRow(array(
            'user_id = ?' => $user->getIdentity(),
            'active = ?' => true,
        ));

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
            if ($package->isFree()) {
                $subscription->setActive(true);
                $subscription->onPaymentSuccess();
                if ($currentSubscription) {
                    $currentSubscription->cancel();
                }
            }

            $subscription_id = $subscription->subscription_id;

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $this->_session->subscription_id = $subscription_id;

        // Check if the user is good (this will happen if they choose a free plan)
        $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
        if ($package->isFree() && $subscriptionsTable->check($this->_user)) {
            return $this->_finishPayment($package->isFree() ? 'free' : 'active');
        }

        // Otherwise redirect to the payment page
        return $this->_helper->redirector->gotoRoute(array('action' => 'gateway'));
    }

    public function subscriptionGatewayAction()
    {
        // Get subscription
        $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
        if (!$subscriptionId ||
            !($subscription = Engine_Api::_()->getItem('payment_subscription', $subscriptionId))
        ) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'choose'));
        }
        $this->view->subscription = $subscription;

        // Check subscription status
        if ($this->_checkSubscriptionStatus($subscription)) {
            return;
        }

        // Get subscription
        if (!$this->_user ||
            !($subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id)) ||
            !($subscription = Engine_Api::_()->getItem('payment_subscription', $subscriptionId)) ||
            $subscription->user_id != $this->_user->getIdentity() ||
            !($package = Engine_Api::_()->getItem('payment_package', $subscription->package_id))
        ) {
            return $this->_helper->redirector->gotoRoute(array('action' => 'choose'));
        }

        // Unset certain keys
        unset($this->_session->gateway_id);
        unset($this->_session->order_id);

        // Gateways
        $gatewayTable = Engine_Api::_()->getDbtable('gateways', 'payment');
        $gatewaySelect = $gatewayTable->select()
            ->where('enabled = ?', 1);
        $gateways = $gatewayTable->fetchAll($gatewaySelect);

        $gatewayPlugins = array();
        foreach ($gateways as $gateway) {
            // Check billing cycle support
            if (!$package->isOneTime()) {
                $sbc = $gateway->getGateway()->getSupportedBillingCycles();
                if (!in_array($package->recurrence_type, array_map('strtolower', $sbc))) {
                    continue;
                }
            }
            $gatewayPlugins[] = array(
                'gateway' => $gateway,
                'plugin' => $gateway->getGateway(),
            );
        }

        $form = new Engine_Form();
        $form->setAction(
            $this->view->escape($this->view->url(array('action' => 'process')))
        );
        $form->addElement(
            new Zend_Form_Element_Hidden('gateway_id')
        );
        foreach ($gatewayPlugins as $gatewayInfo) {
            $gateway = $gatewayInfo['gateway'];
            $plugin = $gatewayInfo['plugin'];
            $first = (!isset($first) ? true : false);
            $prependText = '';
            if (!$first) {
                $prependText = $this->view->translate(' or ');
            }
            $button = new Zend_Form_Element_Submit('button_' . $gateway->gateway_id, array(
                'onclick' => "$('#gateway_id').attr('value', '" . $gateway->gateway_id . "');",
                'label' => $this->view->translate('Pay with %1$s', $this->view->translate($gateway->title)),
                'prependText' => $prependText
            ));
            $form->addElement($button);
        }

        $description = $this->view->translate('You have selected an account type that requires ' .
            'recurring subscription payments. You will be taken to a secure ' .
            'checkout area where you can setup your subscription. Remember to ' .
            'continue back to our site after your purchase to sign in to your ' .
            'account.');
        if ($package->recurrence) {
            $recurrence = $this->view->translate('Please setup your subscription to continue:');
        } else {
            $recurrence = $this->view->translate('Please pay a one-time fee to continue:');
        }

        $params = array(
            'title' => $this->view->translate('Pay for Access'),
            'description' => $description,
            'recurrence' => $recurrence
        );

        $this->add($this->component()->paymentGateway($params));
        if ($this->_getParam('state') != 'pending') {
            $this->add($this->component()->form($form));
        }
        $this->renderContent();
    }

    public function subscriptionProcessAction()
    {
        // Get gateway
        $this->add($this->component()->html('<div id="payment_loading">' . $this->view->translate('Please Wait') . '</div>'));
        $gatewayId = $this->_getParam('gateway_id', $this->_session->gateway_id);
        if (!$gatewayId ||
            !($gateway = Engine_Api::_()->getItem('payment_gateway', $gatewayId)) ||
            !($gateway->enabled)
        ) {
            $this->redirect($this->view->url(array('action' => 'gateway')));
        }
        $this->view->gateway = $gateway;

        // Get subscription
        $subscriptionId = $this->_getParam('subscription_id', $this->_session->subscription_id);
        if (!$subscriptionId ||
            !($subscription = Engine_Api::_()->getItem('payment_subscription', $subscriptionId))
        ) {
            $this->redirect($this->view->url(array('action' => 'choose')));
        }
        $this->view->subscription = $subscription;

        // Get package
        $package = $subscription->getPackage();
        if (!$package || $package->isFree()) {
            $this->redirect($this->view->url(array('action' => 'choose')));
        }
        $this->view->package = $package;

        // Check subscription?
        if ($this->_checkSubscriptionStatus($subscription)) {
            return;
        }


        // Process

        // Create order
        $ordersTable = Engine_Api::_()->getDbtable('orders', 'payment');
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
            'source_type' => 'payment_subscription',
            'source_id' => $subscription->subscription_id,
        ));
        $this->_session->order_id = $order_id = $ordersTable->getAdapter()->lastInsertId();

        // Unset certain keys
        unset($this->_session->package_id);
        unset($this->_session->subscription_id);
        unset($this->_session->gateway_id);


        // Get gateway plugin
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();


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
        $params['vendor_order_id'] = $order_id;
        $params['return_url'] = $schema . $host
            . $this->view->url(array('action' => 'return'))
            . '?order_id=' . $order_id
            //. '?gateway_id=' . $this->_gateway->gateway_id
            //. '&subscription_id=' . $this->_subscription->subscription_id
            . '&state=' . 'return';
        $params['cancel_url'] = $schema . $host
            . $this->view->url(array('action' => 'return'))
            . '?order_id=' . $order_id
            //. '?gateway_id=' . $this->_gateway->gateway_id
            //. '&subscription_id=' . $this->_subscription->subscription_id
            . '&state=' . 'cancel';
        $params['ipn_url'] = $schema . $host
            . $this->view->url(array('action' => 'index', 'controller' => 'ipn'))
            . '?order_id=' . $order_id;
        //. '?gateway_id=' . $this->_gateway->gateway_id
        //. '&subscription_id=' . $this->_subscription->subscription_id;

        // Process transaction
        $transaction = $plugin->createSubscriptionTransaction($this->_user,
            $subscription, $package, $params);

        // Pull transaction params
        $this->view->transactionUrl = $transactionUrl = $gatewayPlugin->getGatewayUrl();
        $this->view->transactionMethod = $transactionMethod = $gatewayPlugin->getGatewayMethod();
        $this->view->transactionData = $transactionData = $transaction->getData();

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
        // Handle redirection
        if ($transactionMethod == 'GET') {
            $transactionUrl .= '?' . http_build_query($transactionData);
            $this->redirect($transactionUrl);
            //            return $this->_helper->redirector->gotoUrl($transactionUrl, array('prependBase' => false));
        }
        $this->renderContent();
    }

    public function subscriptionReturnAction()
    {
        // Get order
        if (!$this->_user ||
            !($orderId = $this->_getParam('order_id', $this->_session->order_id)) ||
            !($order = Engine_Api::_()->getItem('payment_order', $orderId)) ||
            $order->user_id != $this->_user->getIdentity() ||
            $order->source_type != 'payment_subscription' ||
            !($subscription = $order->getSource()) ||
            !($package = $subscription->getPackage()) ||
            !($gateway = Engine_Api::_()->getItem('payment_gateway', $order->gateway_id))
        ) {
            $this->redirect($this->view->url(array(), 'default', true));
        }

        // Get gateway plugin
        $this->view->gatewayPlugin = $gatewayPlugin = $gateway->getGateway();
        $plugin = $gateway->getPlugin();

        // Process return
        unset($this->_session->errorMessage);
        try {
            $status = $plugin->onSubscriptionTransactionReturn($order, $this->_getAllParams());
        } catch (Payment_Model_Exception $e) {
            $status = 'failure';
            $this->_session->errorMessage = $e->getMessage();
        }

        return $this->_finishPayment($status);
    }

    public function subscriptionFinishAction()
    {
        $status = $this->_getParam('state');
        $error = $this->_session->errorMessage;
        $this->view->params = array(
            'status' => $status,
            'error' => $error
        );
        $caption = $this->view->translate('Undefined caption.');
        $description = $this->view->translate('Undefined description.');
        $error = $this->view->translate('Error.');
        $error = $this->view->translate('Error name.');
        $url = $this->view->escape($this->view->url(array(), 'default', true));
        if($status == 'pending') {
            $caption = $this->view->translate('Payment Pending');
            $description = $this->view->translate('Thank you for submitting your ' .
                          'payment. Your payment is currently pending - your account ' .
                          'will be activated when we are notified that the payment has ' .
                          'completed successfully. Please return to our login page ' .
                          'when you receive an email notifying you that the payment ' .
                          'has completed.');
            $button = $this->view->translate('Back to Home');
        } elseif($status == 'active') {
            $caption = $this->view->translate('Payment Complete');
            $description = $this->view->translate('Thank you! Your payment has completed successfully.');
            $button = $this->view->translate('Continue');
        } else { // failed
            $caption = $this->view->translate('Payment Failed');
            $description = $this->view->translate('Our payment processor has notified ' .
                            'us that your payment could not be completed successfully. ' .
                            'We suggest that you try again with another credit card ' .
                            'or funding source.');
            $button = $this->view->translate('Back to Home');

            if (!$this->_session->__isset('errorMessage')) {
                $description = $error = 'There was an error processing your transaction. Please try again later.';
            } else {
                $description = $error = $this->_session->__get('errorMessage');
                $errorName = $this->_session->__get('errorName');
            }
            if (empty($error)) {
                $description = $error = $this->view->translate('Our payment processor has notified ' .
                    'us that your payment could not be completed successfully. ' .
                    'We suggest that you try again with another credit card ' .
                    'or funding source.');
            }
            $string = '';
            if (is_array($error)) {
                foreach ($error as $err)
                    $string .= "<p>{$err}</p>";
                $description = $error = $string;
            }
        }


        $params = array(
            'url' => $url,
            'status' => $status,
            'error' => $error,
            'errorName' => $errorName,
            'caption' => $caption,
            'description' => $description
        );
        $this->add($this->component()->transactionFinish($params))->renderContent();
        $this->_session->unsetAll();
    }

    protected function _checkSubscriptionStatus(Zend_Db_Table_Row_Abstract $subscription = null)
    {
        if (!$this->_user) {
            return false;
        }

        if (null === $subscription) {
            $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
            $subscription = $subscriptionsTable->fetchRow(array(
                'user_id = ?' => $this->_user->getIdentity(),
                'active = ?' => true,
            ));
        }

        if (!$subscription) {
            return false;
        }

        if ($subscription->status == 'active' ||
            $subscription->status == 'trial'
        ) {
            if (!$subscription->getPackage()->isFree()) {
                $this->_finishPayment('active');
            } else {
                $this->_finishPayment('free');
            }
            return true;
        } else if ($subscription->status == 'pending') {
            $this->_finishPayment('pending');
            return true;
        }

        return false;
    }

    protected function _finishPayment($state = 'active')
    {
        $viewer = Engine_Api::_()->user()->getViewer();
        $user = $this->_user;

        // No user?
        if (!$this->_user) {
            $this->redirect($this->view->url(array(), 'default', true));
        }
        // Log the user in, if they aren't already
        if (($state == 'active' || $state == 'free') &&
            $this->_user &&
            !$this->_user->isSelf($viewer) &&
            !$viewer->getIdentity()
        ) {
            Zend_Auth::getInstance()->getStorage()->write($this->_user->getIdentity());
            Engine_Api::_()->user()->setViewer();
            $viewer = $this->_user;
        }
        // Handle email verification or pending approval
        if ($viewer->getIdentity() && !$viewer->enabled) {
            Engine_Api::_()->user()->setViewer(null);
            Engine_Api::_()->user()->getAuth()->getStorage()->clear();

            $confirmSession = new Zend_Session_Namespace('Signup_Confirm');
            $confirmSession->approved = $viewer->approved;
            $confirmSession->verified = $viewer->verified;
            $confirmSession->enabled = $viewer->enabled;
            $this->redirect($this->view->url(array('action' => 'confirm'), 'user_signup', true));
        }
        // Clear session
        $errorMessage = $this->_session->errorMessage;
        $userIdentity = $this->_session->user_id;
        $this->_session->unsetAll();
        $this->_session->user_id = $userIdentity;
        $this->_session->errorMessage = $errorMessage;
        // Redirect
        if ($state == 'free') {
            $this->redirect($this->view->url(array(), 'default', true));
        } else {
            $this->redirect($this->view->url(array('action' => 'finish', 'state' => $state)));
        }
    }

    protected function _checkDefaultPaymentPlan()
    {
        // No user?
        if (!$this->_user) {
            $this->redirect($this->view->url(array(), 'default', true));
        }

        // Handle default payment plan
        try {
            $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
            if ($subscriptionsTable) {
                $subscription = $subscriptionsTable->activateDefaultPlan($this->_user);
                if ($subscription) {
                    $this->_finishPayment('free');
                }
            }
        } catch (Exception $e) {
            // Silence
        }

        // Fall-through
    }
    /**
     *   Subscription Controller
     */
}
