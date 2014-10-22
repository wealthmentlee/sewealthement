<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright 2009-2012 Hire-Experts
 * @license    http://hire-experts.com/
 * @version    $Id: RateController.php 2012-08-16 01:09:21Z taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright 2009-2012 Hire-Experts
 * @license    http://hire-experts.com/
 */

class Apptouch_RateController extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->_helper->contextSwitch->initContext();

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('rate', 'json')->initContext('json');
    $ajaxContext->addActionContext('getratecontainer', 'json')->initContext('json');
  }

  public function indexIndexAction()
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

    if (!$item) {
      $this->_helper->layout->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    $table = Engine_Api::_()->getDbtable('rates', 'rate');
    $this->view->rate_info = $rate_info = $table->fetchRateInfo($item_type, $item_id);

    $this->view->maxRate = 5; // todo edit stars count

    $item_score = ($rate_info && $rate_info['rate_count']) ? $rate_info['total_score'] / $rate_info['rate_count'] : 0;

    $this->view->assign('item_score', round($item_score, 2));

    $urlOptions = array(
      'module' => 'apptouch',
      'controller' => 'rate',
      'action' => 'index-index',
      'type' => $item_type,
      'id' => $item_id
    );

    $this->view->assign('rate_url', $this->_helper->url->url($urlOptions, 'default'));
    $this->view->assign('rate_uid', uniqid('rate_'));
    $this->view->item_type = $item_type;
    $this->view->can_rate = Zend_Json::encode(array('can_rate' => $can_rate, 'error_msg' => $error_msg));
  }

  public function indexRateAction()
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
      $this->view->result = false;
      $this->view->message = $translate->_('Sorry, guests cannot rate. Please login to continue.');
      return;
    }

    if (!Engine_Api::_()->authorization()->isAllowed('rate', null, 'enabled')) {
      $this->view->result = false;
      $this->view->message = $translate->_('Sorry, you cannot rate this content.');
      return;
    }

    if ($item->getOwner()->getIdentity() == $viewer->getIdentity() && !$allowRateToOwnContent) {
      $this->view->result = false;
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

        if ($is_create) {
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
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
    }

    return;
  }

  public function indexGetratecontainerAction()
  {

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

  public function reviewCreateReviewAction()
  {
    $result = 0;
    $page_id = (int)$this->_getParam('page_id');

    $tbl_page = Engine_Api::_()->getDbTable('pages', 'page');
    $page = $tbl_page->findRow($page_id);

    $viewer = Engine_Api::_()->user()->getViewer();

    // if page and viewer exists
    if ($page && $viewer->getIdentity()){

      $form = new Rate_Form_Review_Create();
      $form->removeAttrib('onsubmit');
      $form->getElement('cancel')->setAttrib('onClick', '');
      $types = Engine_Api::_()->getApi('core', 'rate')->getPageTypes($page->getIdentity());

      // Add vote types
      $form->addVotes($types);

      $this->addPageInfo('contentTheme', 'd');
      if ( !$this->getRequest()->isPost() ) {
        $this->add($this->component()->subjectPhoto($page))
          ->add($this->component()->form($form))
          ->renderContent();
        return;
      }

      if ( !$form->isValid($this->getRequest()->getPost()) ) {
        $this->add($this->component()->subjectPhoto($page))
          ->add($this->component()->form($form))
          ->renderContent();
        return;
      }


      $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');

      if ($tbl->isAllowedPost($page->getIdentity(), $viewer)){

        $values = $form->getValues();

        $values['user_id'] = $viewer->getIdentity();
        $values['page_id'] = $page_id;
        $values['creation_date'] = date('Y-m-d H:i:s');
        $values['modified_date'] = date('Y-m-d H:i:s');

        $row = $tbl->createRow($values);
        $result = (bool)$row->save();

        if ($result){

          // Add Votes
          $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
          $type_keys = array();
          foreach ($types as $type){
            $type_keys[$type->type_id] = 'rate_'.$type->type_id;
          }

          foreach ($values as $key=>$value){

            if ($type_id = array_search($key, $type_keys)){

              $tbl_vote->createRow(array(
                'type_id' => $type_id,
                'review_id' => $row->getIdentity(),
                'page_id' => $page->getIdentity(),
                'rating' => ($value <= 5 || $value >= 0) ? (int)$value : 0,
                'creation_date' => date('Y-m-d H:i:s')
              ))->save();

            }
          }

          // Add Search
          Engine_Api::_()->getDbTable('search', 'page')->saveData($row);

          // Add Action
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $link = $row->getLink();
          $page = $row->getPage();

          $action = $api->addActivity($viewer, $page, 'pagereview_new', null, array('is_mobile' => true, 'link' => $link));
          $api->attachActivity($action, $row, Activity_Model_Action::ATTACH_DESCRIPTION);

          Engine_Api::_()->page()->sendNotification($row, 'post_pagereview');
        }
      }

    }
    $this->view->message = $this->view->translate(($result) ? 'RATE_REVIEW_CREATE_SUCCESS'
      : 'RATE_REVIEW_CREATE_ERROR');

    return $this->redirect($row->getHref());
  }

  public function reviewEditAction()
  {

    $page_id = (int)$this->_getParam('page_id');
    $page = Engine_Api::_()->getItem('page', $page_id);
    $review_id = (int)$this->_getParam('pagereview_id');

    $viewer = Engine_Api::_()->user()->getViewer();

    $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
    $row = $tbl->findRow($review_id);

    if( !$row || !$row->isOwner($viewer) ) {
      $this->view->message = '';
      return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'reviews'), 'page_view'));
    }

    $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
    $select = $tbl_vote->select()
      ->where('review_id = ?', $row->getIdentity());
    $votes = $tbl_vote->fetchAll($select);

    $vote_list = array();
    foreach ($votes as $vote){
      $vote_list[$vote->type_id] = $vote->rating;
    }

    $types = Engine_Api::_()->getApi('core', 'rate')->getPageTypes($page_id);
    foreach ($types as $key => $type){
      if (isset($vote_list[$type->type_id])){
        $types[$key]->value = $vote_list[$type->type_id];
      }
    }

    $form = new Rate_Form_Review_Edit();
    $form->removeAttrib('onsubmit');
    $form->getElement('cancel')->setAttrib('onClick', '');
    $form->addVotes($types);

    // Set Form Values
    $form->pagereview_id->setValue($row->getIdentity());
    $form->title->setValue($row->title);
    $form->body->setValue($row->body);

    $result = false;

    $this->addPageInfo('contentTheme', 'd');

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->subjectPhoto($page))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->subjectPhoto($page))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $values = $form->getValues();
    $values['modified_date'] = date('Y-m-d H:i:s');
    $row->setFromArray($values);
    $result = (bool)$row->save();

    if ($result){

      // Delete Old Votes
      $tbl_vote->delete(array(
        'review_id = ?' => $row->getIdentity()
      ));

      $type_keys = array();
      foreach ($types as $type){
        $type_keys[$type->type_id] = 'rate_'.$type->type_id;
      }
      foreach ($values as $key=>$value){
        if ($type_id = array_search($key, $type_keys)){
          $tbl_vote->createRow(array(
            'type_id' => $type_id,
            'review_id' => $row->getIdentity(),
            'page_id' => $page_id,
            'rating' => ($value <= 5 || $value >= 0) ? (int)$value : 0,
            'creation_date' => date('Y-m-d H:i:s')
          ))->save();
        }
      }

      // Delete and Create Search
      $tbl_search = Engine_Api::_()->getDbTable('search', 'page');
      $tbl_search->saveData($row);
    }

    $this->view->message = $this->view->translate(($result) ? 'RATE_REVIEW_EDIT_SUCCESS' : 'RATE_REVIEW_EDIT_ERROR' );

    return $this->redirect($row->getHref());
  }

  public function reviewRemoveAction(){
    $page_id = $this->_getParam('page_id');

    if (!$page_id) {
      return $this->redirect($this->view->url(array(), 'page_browse'));
    }

    $page = Engine_Api::_()->getItem('page', $page_id);
    $review_id = $this->_getParam('pagereview_id');

    if (!$review_id) {
      return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'reviews'), 'page_view', true));
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $tbl = Engine_Api::_()->getDbTable('pagereviews', 'rate');
    $row = $tbl->findRow($review_id);

    if (!$row) {
      return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'reviews'), 'page_view', true));
    }

    if (!$row->isOwner($viewer) && !Engine_Api::_()->getApi('core', 'rate')->isAllowRemoveReview($page_id, $viewer)) {
      return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'reviews'), 'page_view', true));
    }
    $form = $this->getForm();

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

    $result = false;

    $result = (bool)$row->delete();


    $this->view->result = $result;
    $this->view->message = $this->view->translate(($result) ? 'RATE_REVIEW_DELETE_SUCCESS' : 'RATE_REVIEW_DELETE_ERROR');

    return $this->redirect($this->view->url(array('page_id' => $page->url, 'tab' => 'reviews'), 'page_view', true));
  }


  public function offerReviewRemoveAction()
  {
    $result = false;
    $review_id = $this->_getParam('review_id');

    $viewer = Engine_Api::_()->user()->getViewer();

    $tbl = Engine_Api::_()->getDbTable('offerreviews', 'rate');
    $row = $tbl->findRow($review_id);

    if ($row) {

      $offer_id = $row->offer_id;

      if ($viewer->isOwner($row->getOwner()) || Engine_Api::_()->getApi('core', 'rate')
        ->isAllowRemoveReview($offer_id, $viewer)
      ) {
        $result = (bool)$row->delete();
      }
    }
    $this->view->result = $result;
    $this->view->message = $this->view->translate(($result) ? 'RATE_REVIEW_DELETE_SUCCESS' : 'RATE_REVIEW_DELETE_ERROR');

    return $this->redirect('parentRefresh');
  }

  public function offerReviewCreateReviewAction()
  {
    $result = 0;
    $offer_id = (int)$this->_getParam('offer_id');

    $tbl_page = Engine_Api::_()->getDbTable('offers', 'offers');
    $offer = $tbl_page->findRow($offer_id);

    $viewer = Engine_Api::_()->user()->getViewer();

    // if page and viewer exists
    if ($offer && $viewer->getIdentity()){

      $form = new Rate_Form_OfferReview_Create();
      $form->removeAttrib('onsubmit');
      $form->getElement('cancel')->setAttrib('onClick', '');
      $types = Engine_Api::_()->getApi('core', 'rate')->getOfferTypes($offer->getIdentity());

      // Add vote types
      $form->addVotes($types);

      $this->addPageInfo('contentTheme', 'd');
      if ( !$this->getRequest()->isPost() ) {
        $this->add($this->component()->subjectPhoto($offer))
          ->add($this->component()->form($form))
          ->renderContent();
        return;
      }

      if ( !$form->isValid($this->getRequest()->getPost()) ) {
        $this->add($this->component()->subjectPhoto($offer))
          ->add($this->component()->form($form))
          ->renderContent();
        return;
      }


      $tbl = Engine_Api::_()->getDbTable('offerreviews', 'rate');

      if ($tbl->isAllowedPost($offer->getIdentity(), $viewer)){

        $values = $form->getValues();

        $values['user_id'] = $viewer->getIdentity();
        $values['offer_id'] = $offer_id;
        $values['creation_date'] = date('Y-m-d H:i:s');
        $values['modified_date'] = date('Y-m-d H:i:s');

        $row = $tbl->createRow($values);
        $result = (bool)$row->save();

        if ($result){

          // Add Votes
          $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
          $type_keys = array();
          foreach ($types as $type){
            $type_keys[$type->type_id] = 'rate_'.$type->type_id;
          }

          foreach ($values as $key=>$value){

            if ($type_id = array_search($key, $type_keys)){

              $tbl_vote->createRow(array(
                'type_id' => $type_id,
                'review_id' => $row->getIdentity(),
                'offer_id' => $offer->getIdentity(),
                'rating' => ($value <= 5 || $value >= 0) ? (int)$value : 0,
                'creation_date' => date('Y-m-d H:i:s')
              ))->save();

            }
          }

          // Add Action
          $api = Engine_Api::_()->getDbtable('actions', 'activity');
          $link = $row->getLink();
          $offer = $row->getOffer();

          $action = $api->addActivity($viewer, $offer, 'offerreview_new', null, array('is_mobile' => true, 'link' => $link));
          $api->attachActivity($action, $row, Activity_Model_Action::ATTACH_DESCRIPTION);

          $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
          $notifyApi->addNotification($offer->getOwner(), $viewer, $row, 'post_offerreview', array(
            'label' => $row->getShortType()
          ));
        }
      }

    }
    $this->view->message = $this->view->translate(($result) ? 'RATE_REVIEW_CREATE_SUCCESS' : 'RATE_REVIEW_CREATE_ERROR');

    return $this->redirect($offer->getHref());
  }

  public function offerReviewEditAction()
  {
    $offer_id = (int)$this->_getParam('offer_id');
    $offer = Engine_Api::_()->getItem('offer', $offer_id);
    $review_id = (int)$this->_getParam('review_id');

    $viewer = Engine_Api::_()->user()->getViewer();

    $tbl = Engine_Api::_()->getDbTable('offerreviews', 'rate');
    $row = $tbl->findRow($review_id);

    if( !$row || !$row->isOwner($viewer) ) {
      $this->view->message = '';
      return $this->redirect('parentRefresh');
    }

    $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
    $select = $tbl_vote->select()
      ->where('review_id = ?', $row->getIdentity());
    $votes = $tbl_vote->fetchAll($select);

    $vote_list = array();
    foreach ($votes as $vote){
      $vote_list[$vote->type_id] = $vote->rating;
    }

    $types = Engine_Api::_()->getApi('core', 'rate')->getOfferTypes($offer_id);
    foreach ($types as $key => $type){
      if (isset($vote_list[$type->type_id])){
        $types[$key]->value = $vote_list[$type->type_id];
      }
    }

    $form = new Rate_Form_OfferReview_Edit($offer->isUsed());
    $form->removeAttrib('onsubmit');
    $form->getElement('cancel')->setAttrib('onClick', '');
    $form->addVotes($types);

    // Set Form Values
    $form->offerreview_id->setValue($row->getIdentity());
    $form->title->setValue($row->title);
    $form->body->setValue($row->body);

    $result = false;

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->subjectPhoto($offer))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->subjectPhoto($offer))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $values = $form->getValues();
    $values['modified_date'] = date('Y-m-d H:i:s');
    $row->setFromArray($values);
    $result = (bool)$row->save();

    if ($result){

      // Delete Old Votes
      $tbl_vote->delete(array(
        'review_id = ?' => $row->getIdentity()
      ));

      $type_keys = array();
      foreach ($types as $type){
        $type_keys[$type->type_id] = 'rate_'.$type->type_id;
      }
      foreach ($values as $key=>$value){
        if ($type_id = array_search($key, $type_keys)){
          $tbl_vote->createRow(array(
            'type_id' => $type_id,
            'review_id' => $row->getIdentity(),
            'offer_id' => $offer_id,
            'rating' => ($value <= 5 || $value >= 0) ? (int)$value : 0,
            'creation_date' => date('Y-m-d H:i:s')
          ))->save();
        }
      }
    }

    $this->view->message = $this->view->translate(($result) ? 'RATE_REVIEW_EDIT_SUCCESS' : 'RATE_REVIEW_EDIT_ERROR' );

    return $this->redirect('parentRefresh');
  }

  public function offerReviewViewAction()
  {
    $review = Engine_Api::_()->getDbTable('offerreviews', 'rate')->findRow($this->_getParam('review_id'));
    $offer = $review->getOffer();
    $owner = $review->getOwner();

    $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
    $select = $tbl_vote->select()
      ->where('review_id = ?', $review->getIdentity());
    $votes = $tbl_vote->fetchAll($select);

    $vote_list = array();
    foreach ($votes as $vote) {
      $vote_list[$vote->type_id] = $vote->rating;
    }
    $types = Engine_Api::_()->getApi('core', 'rate')->getOfferTypes($review->offer_id);
    foreach ($types as $key => $type) {
      if (isset($vote_list[$type->type_id])) {
        $types[$key]->value = $vote_list[$type->type_id];
      }
    }

    $html = '';
    foreach ($types as $type) {
      $html = $html . '<div class="review_stars">';
      $html = $html . $this->view->reviewRate($type->value);
      $html = $html . '<div class="title">' . $type->label . '</div>';
      $html = $html . '</div>';
    }

    $option = array(
      array(
        'label' => $this->view->translate('RATE_REVIEW_BACK'),
        'attrs' => array(
          'href' => $offer->getHref()
        )
      )
    );

    $this->add($this->component()->quickLinks('gutter'))
      ->add($this->component()->subjectPhoto($review->getOffer()))
      ->add($this->component()->html($review->title))
      ->add($this->component()->date(array('title' => $this->view->translate('Posted by') . ' ' . $owner->getTitle() . ' ' . $this->view->timestamp($review->creation_date), 'count' => null)))
      ->add($this->component()->html($review->body))
      ->add($this->component()->html($html))
      ->add($this->component()->comments(array('subject' => $review)))
      ->add($this->component()->customComponent('navigation', $option))
      ->renderContent();
  }

  private function getForm()
  {
    $form = new Engine_Form();
    $form->setTitle('RATE_REVIEW_DELETE');
    $form->setDescription('RATE_REVIEW_DELETEDESC');
    $form->addElement('Button', 'submit', array(
      'type' => 'submit',
      'label' => 'Confirm'
    ));

    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or '
    ));

    return $form;
  }
}
