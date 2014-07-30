<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Rate_IndexController extends Core_Controller_Action_Standard
{

  public function rateAction()
  {
    $item_type = $this->_getParam('type', 'quiz');
    $item_id = $this->_getParam('id', 0);
    $score = $this->_getParam('score', 0);

    $item = Engine_Api::_()->getItem($item_type, $item_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    //$settings = Engine_Api::_()->getApi('settings', 'core');
    //$this->view->maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
    $this->view->maxRate = 5; // todo edit stars count

    if (!$item || !$viewer->getIdentity() || !$score || $score > $this->view->maxRate) {
      return $this->_forward('error', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Sorry, guests cannot rate. Please login to continue.')),
        'return_url'=>urldecode($this->_getParam('return_url')),
      ));
    }

    if ( !Engine_Api::_()->authorization()->isAllowed('rate', null, 'enabled') && false) {
      return $this->_forward('error', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Sorry, you cannot rate this content.')),
        'return_url'=>urldecode($this->_getParam('return_url')),
      ));
    }

    if ($item->getOwner()->getIdentity() == $viewer->getIdentity()) {
      return $this->_forward('error', 'utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Sorry, you cannot rate own content.')),
        'return_url'=>urldecode($this->_getParam('return_url')),
      ));
    } else {
      $table = Engine_Api::_()->getDbtable('rates', 'rate');
      $db = $table->getAdapter();

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
            'label' => $item->getShortType()
          ));
        }

      }
      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
    }

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => array(Zend_Registry::get('Zend_Translate')->_('You have successfully rated this content.')),
      'return_url'=>urldecode($this->_getParam('return_url')),
    ));
  }

}