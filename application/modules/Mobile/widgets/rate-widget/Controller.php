<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Mobile_Widget_RateWidgetController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate')){
      return $this->setNoRender();
    }
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('mobile.show.rate-widget', 1)){
      return $this->setNoRender();
    }

    $subject = (Engine_Api::_()->core()->hasSubject()) ? Engine_Api::_()->core()->getSubject() : null;

    if (!$subject || !$subject->getIdentity()) {
      return $this->setNoRender();
    }

    if ($subject->getType() == 'page'){
      if (!Engine_Api::_()->mobile()->checkPageWidget($subject->getIdentity(), 'mobile.rate-widget')){
        return $this->setNoRender();
      }
    }

    $this->getElement()->setTitle('');

    $this->view->item_type = $item_type = strtolower($subject->getType());
    $this->view->item_id = $item_id = $subject->getIdentity();

    $this->view->isReview = false;

    if ($subject->getType() == 'page'){

      $this->view->subject = $subject = Engine_Api::_()->core()->getSubject('page');
      $this->view->pageId = $page_id = $subject->getIdentity();

      $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
      $select = $tbl_vote->select()
          ->from( $tbl_vote->info('name'), new Zend_Db_Expr("type_id, SUM(rating) AS rating, COUNT(*) AS total") )
          ->where('page_id = ?', $page_id)
          ->group('type_id');
      $ratings = $tbl_vote->getAdapter()->fetchAll($select);

      $rating_list = array();
      foreach ($ratings as $rating){
        $rating_list[$rating['type_id']] = round($rating['rating'] / $rating['total'], 2);
      }
      $types = Engine_Api::_()->getApi('core', 'rate')->getPageTypes($page_id);
      foreach ($types as $key=>$type){
        if (array_key_exists($type->type_id, $rating_list)){
          $types[$key]->value = $rating_list[$type->type_id];
        }
      }
      $this->view->types = $types;
      $this->view->isReview = true;

      if (!count($types)){
        return $this->setNoRender();
      }

    } else {

      if (!Engine_Api::_()->rate()->isSupportedPlugin($item_type)) {
        return $this->setNoRender();
      }

      $this->view->item = $subject;

      $table = Engine_Api::_()->getDbtable('rates', 'rate');
      $this->view->rate_info = $rate_info = $table->fetchRateInfo($item_type, $item_id);
      $item_score = ($rate_info && $rate_info['rate_count']) ? $rate_info['total_score'] / $rate_info['rate_count'] : 0;
      $this->view->assign('item_score', round($item_score, 2));

  //  $settings = Engine_Api::_()->getApi('settings', 'core');
  //  $this->view->maxRate = $settings->getSetting('rate.' . $subject . '.max.rate', 5);
      $this->view->maxRate = 5; // todo edit stars count

      $can_rate = $this->_getParam('can_rate', true);
      $error_msg = $this->_getParam('error_msg', '');

      $front_router = Zend_Controller_Front::getInstance()->getRouter();

      $this->view->assign('rate_url', $front_router->assemble(array('module' => 'rate'), 'widget_rate'));
      $this->view->assign('rate_uid', uniqid('rate_'));
      $this->view->can_rate = Zend_Json::encode(array('can_rate' => $can_rate, 'error_msg' => $error_msg));

    }

  }
}