<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $this->_helper->contextSwitch->initContext();

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('rate', 'json')->initContext('json');
    $ajaxContext->addActionContext('getratecontainer', 'json')->initContext('json');
  }

  public function indexAction()
  {
    $item_type = $this->_getParam('type', 'quiz');
    $item_id = $this->_getParam('id', 0);

    $can_rate = $this->_getParam('can_rate', true);
    $error_msg = $this->_getParam('error_msg', '');

    $translate = Zend_Registry::get('Zend_Translate');

    if (!$can_rate && !$error_msg) {
      $error_msg = $translate->_('Sorry, you cannot rate this content.');
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    
    if ($can_rate && !$viewer->getIdentity()) {
      $can_rate = false;
      $error_msg = $translate->_('Sorry, guests cannot rate. Please login to continue.');
    }

    $this->view->item = $item = Engine_Api::_()->getItem($item_type, $item_id);

    if (!$item)
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        return;
    }

    $table = Engine_Api::_()->getDbtable('rates', 'rate');
    $this->view->rate_info = $rate_info = $table->fetchRateInfo($item_type, $item_id);

    //$settings = Engine_Api::_()->getApi('settings', 'core');
    //$this->view->maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
    $this->view->maxRate = 5; // todo edit stars count

    $item_score = ($rate_info && $rate_info['rate_count']) ? $rate_info['total_score'] / $rate_info['rate_count'] : 0;

    $this->view->assign('item_score', round($item_score, 2));

    $urlOptions = array(
      'module' => 'rate',
      'controller' => 'index',
      'action' => 'rate',
      'type' => $item_type,
      'id' => $item_id
    );

    $this->view->assign('rate_url', $this->_helper->url->url($urlOptions, 'default'));
    $this->view->assign('rate_uid', uniqid('rate_'));
    $this->view->item_type = $item_type;
    $this->view->can_rate = Zend_Json::encode(array('can_rate' => $can_rate, 'error_msg' => $error_msg));
  }

  public function rateAction()
  {
    $item_type = $this->_getParam('type', 'quiz');
    $item_id = $this->_getParam('id', 0);
    $score = $this->_getParam('score', 0);

    $item = Engine_Api::_()->getItem($item_type, $item_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $allowRateToOwnContent = $settings->getSetting('rate.own.' . $item_type . '.enabled', false);
    $this->view->maxRate = 5; // todo edit stars count
    $translate = Zend_Registry::get('Zend_Translate');
    
    if (!$item || !$viewer->getIdentity() || !$score || $score > $this->view->maxRate) {
      $this->view->result  = false;
      $this->view->message = $translate->_('Sorry, guests cannot rate. Please login to continue.');
      return;
    }

    if ( !Engine_Api::_()->authorization()->isAllowed('rate', null, 'enabled')) {
      $this->view->result  = false;
      $this->view->message = $translate->_('Sorry, you cannot rate this content.');
      return;
    }

    if ($item->getOwner()->getIdentity() == $viewer->getIdentity() && !$allowRateToOwnContent) {
      $this->view->result  = false;
      $this->view->message = $translate->_('Sorry, you cannot rate own content.');
    } else {
      $table = Engine_Api::_()->getDbtable('rates', 'rate');
      $db = $table->getAdapter();
      $db->beginTransaction();
      try
      {
        $userRate = $table->fetchUserRate($item_type, $item_id, $viewer->getIdentity());

        $is_create = false;
        if (!$userRate) {
          $userRate = $table->createRow();
          $is_create = true;
        }

        $userRate->object_type = $item_type;
        $userRate->object_id = $item_id;
        $userRate->user_id = $viewer->getIdentity();
        $userRate->score = $score;
        $userRate->rated_date = new Zend_Db_Expr('NOW()');

        $userRate->save();
        $db->commit();

        $rate_info = $table->fetchRateInfo($item_type, $item_id);

        $item_score = ($rate_info && $rate_info['rate_count']) ? $rate_info['total_score'] / $rate_info['rate_count'] : 0;

        $this->view->item_score = round($item_score, 2);
        $this->view->rate_count = ($rate_info) ? $rate_info['rate_count'] : 0;
        $this->view->label = $this->view->translate(array('vote', 'votes', $this->view->rate_count));

        if ($is_create){
          // Send Notify
          $item = Engine_Api::_()->getItem($item_type, $item_id);
          $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
          $notifyApi->addNotification($item->getOwner(), $viewer, $item, 'rated', array(
            'label' => ($item->getType() == 'user') ? $translate->_('RATE_user') : $item->getShortType()
          ));
        }

        $this->view->result = true;
        $this->view->message = $translate->_('You have successfully rated this content.');
      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }

    return;
  }

  public function getratecontainerAction() {

    $item_type = $this->_getParam('item_type', 'blog');
    $item_id = $this->_getParam('item_id', 0);
    $can_rate = $this->_getParam('can_rate', true);
    $error_msg = $this->_getParam('error_msg', '');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $this->view->widget_enabled = $widget_enabled = $settings->getSetting('rate.' . $item_type . '.enabled', true);

    if (!$widget_enabled) {
      $this->_helper->layout->disableLayout();
      return;
    }

    if (!Engine_Api::_()->rate()->isSupportedPlugin($item_type)) {
      $this->_helper->layout->disableLayout(true);
      return;
    }

    $table = Engine_Api::_()->getDbtable('rates', 'rate');
    $this->view->rate_info = $rate_info = $table->fetchRateInfo($item_type, $item_id);

    //$this->view->maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
    $this->view->maxRate = 5; // todo edit stars count

    $item_score = ($rate_info && $rate_info['rate_count']) ? $rate_info['total_score'] / $rate_info['rate_count'] : 0;
    $this->view->assign('item_score', round($item_score, 2));

    $this->view->assign('rate_uid', uniqid('rate_'));
    $this->view->item_type = $item_type;
    $this->view->can_rate = Zend_Json::encode(array('can_rate' => $can_rate, 'error_msg' => $error_msg));

    $urlOptions = array(
      'module' => 'rate',
      'controller' => 'index',
      'action' => 'rate',
      'type' => $item_type,
      'id' => $item_id
    );

    $this->view->assign('rate_url', $this->_helper->url->url($urlOptions, 'default'));

    $lang_vars = array(
      'title' => $this->view->translate('Who has voted?'),
      'list_title1' => $this->view->translate('Everyone'),
      'list_title2' => $this->view->translate('Friends')
    );

    $this->view->assign('lang_vars', $lang_vars);
    $this->view->assign('rate_uid', uniqid('rate_'));

    $this->view->html = $this->view->render('index/getratecontainer.tpl');
  }
}