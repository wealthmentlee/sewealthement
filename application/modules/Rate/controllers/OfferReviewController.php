<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: OfferReviewController.php 2012-09-28 19:27 taalay $
 * @author     TJ
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Rate_OfferReviewController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $this->_helper->contextSwitch->initContext();

    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('rate', 'json')->initContext('json');

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';

    $this->view->addScriptPath($path);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts/offer-review-ajax';

    $this->view->addScriptPath($path);

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('rate');
    $path = dirname($path) . '/views/scripts/offer-review';

    $this->view->addScriptPath($path);
  }

  public function createAction()
  {
    $result = 0;
    $offer_id = (int)$this->_getParam('offer_id');

    $tbl_offer = Engine_Api::_()->getDbTable('offers', 'offers');
    $offer = $tbl_offer->findRow($offer_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    // if offer and viewer exists
    if ($offer && $viewer->getIdentity()) {

      $form = new Rate_Form_OfferReview_Create();

      $types = Engine_Api::_()->getApi('core', 'rate')->getOfferTypes($offer->getIdentity());

      // Add vote types
      $form->addVotes($types);

      if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
        $tbl = Engine_Api::_()->getDbTable('offerreviews', 'rate');
        $values = $form->getValues();
        $values['user_id'] = $viewer->getIdentity();
        $values['creation_date'] = date('Y-m-d H:i:s');
        $values['modified_date'] = date('Y-m-d H:i:s');

        $row = $tbl->createRow($values);
        $result = (bool)$row->save();

        if ($result) {
          // Add Votes
          $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
          $type_keys = array();
          foreach ($types as $type) {
            $type_keys[$type->type_id] = 'rate_' . $type->type_id;
          }

          foreach ($values as $key => $value) {
            if ($type_id = array_search($key, $type_keys)) {
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

          $action = $api->addActivity($viewer, $offer, 'offerreview_new', null, array('link' => $link));
          $api->attachActivity($action, $row, Activity_Model_Action::ATTACH_DESCRIPTION);

          $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
          $notifyApi->addNotification($offer->getOwner(), $viewer, $row, 'post_offerreview', array(
            'label' => $row->getShortType()
          ));
          $this->view->id = $row->getIdentity();
        }

        if ($this->_getParam('mark_as_used')) {
          $subscription = $offer->getSubscription($viewer->getIdentity());
          $subscription->onUsed();
        }
      }
    }

    $this->view->result = $result;
    $this->view->msg = $this->view->translate(($result) ? 'RATE_REVIEW_CREATE_SUCCESS' : 'RATE_REVIEW_CREATE_ERROR');
  }

  public function viewAction()
  {
    $result = false;
    $row = Engine_Api::_()->getDbTable('offerreviews', 'rate')
      ->findRow((int)$this->_getParam('review_id'));

    if ($row) {
      if (!Engine_Api::_()->core()->hasSubject())
        Engine_Api::_()->core()->setSubject($row);

      $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
      $select = $tbl_vote->select()
        ->where('review_id = ?', $row->getIdentity());
      $votes = $tbl_vote->fetchAll($select);

      $vote_list = array();
      foreach ($votes as $vote) {
        $vote_list[$vote->type_id] = $vote->rating;
      }
      $types = Engine_Api::_()->getApi('core', 'rate')->getOfferTypes($row->offer_id);
      foreach ($types as $key => $type) {
        if (isset($vote_list[$type->type_id])) {
          $types[$key]->value = $vote_list[$type->type_id];
        }
      }
      $this->view->types = $types;

      $this->view->owner = $row->getOwner();
      $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
      $this->view->row = $row;

      $this->view->viewAllLikes = $this->_getParam('viewAllLikes', false);
      $this->view->likes = $row->likes()->getLikePaginator();

      $this->view->comment_form_id = "offerreview-comment-form";
      $this->view->page = $page = $this->_getParam('page');
      $this->view->comments = Engine_Api::_()->getApi('core', 'rate')->getComments($page);
      $this->view->isAllowedComment = $row->getOffer()->authorization()->isAllowed($viewer, 'comment');

      if ($this->view->isAllowedComment) {
        $this->view->form = $form = new Core_Form_Comment_Create();
        $form->addElement('Hidden', 'form_id', array('value' => 'offerreview-comment-form'));
        $form->populate(array(
          'identity' => $row->getIdentity(),
          'type' => $row->getType(),
        ));

        $this->view->subject = $row;
        $this->view->likeHtml = $this->view->render('comment/list.tpl');
        $this->view->likeUrl = $this->view->url(array('action' => 'like'), 'like_comment');
        $this->view->unlikeUrl = $this->view->url(array('action' => 'unlike'), 'like_comment');
        $this->view->hintUrl = $this->view->url(array('action' => 'hint'), 'like_comment');
        $this->view->showLikesUrl = $this->view->url(array('action' => 'list'), 'like_comment');
        $this->view->postCommentUrl = $this->view->url(array('action' => 'create'), 'like_comment');

      }
      $result = true;
      $this->view->html = $this->view->render('view.tpl');
    }

    $this->view->result = $result;
  }

  public function editAction()
  {
    $offer_id = (int)$this->_getParam('offer_id');
    $review_id = (int)$this->_getParam('offerreview_id');
    $offer = Engine_Api::_()->getItem('offer', $offer_id);

    $viewer = Engine_Api::_()->user()->getViewer();

    $tbl = Engine_Api::_()->getDbTable('offerreviews', 'rate');
    $row = $tbl->findRow($review_id);

    if ($row && $viewer->isOwner($row->getOwner())) {

      $tbl_vote = Engine_Api::_()->getDbTable('votes', 'rate');
      $select = $tbl_vote->select()
        ->where('review_id = ?', $row->getIdentity());
      $votes = $tbl_vote->fetchAll($select);

      $vote_list = array();
      foreach ($votes as $vote) {
        $vote_list[$vote->type_id] = $vote->rating;
      }

      $types = Engine_Api::_()->getApi('core', 'rate')->getOfferTypes($offer_id);
      foreach ($types as $key => $type) {
        if (isset($vote_list[$type->type_id])) {
          $types[$key]->value = $vote_list[$type->type_id];
        }
      }

      $form = new Rate_Form_OfferReview_Edit($offer->isUsed());

      $this->view->js = implode(" ", $form->addVotes($types));

      if ($this->_getParam('task') == 'dosave') {

        $result = false;

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {

          $values = $form->getValues();

          $values['modified_date'] = date('Y-m-d H:i:s');
          $row->setFromArray($values);
          $result = (bool)$row->save();

          if ($result) {

            // Delete Old Votes
            $tbl_vote->delete(array(
              'review_id = ?' => $row->getIdentity()
            ));

            if ($this->_getParam('mark_as_used')) {
              $subscription = $offer->getSubscription($viewer->getIdentity());
              $subscription->onUsed();
            }

            $type_keys = array();
            foreach ($types as $type) {
              $type_keys[$type->type_id] = 'rate_' . $type->type_id;
            }
            foreach ($values as $key => $value) {
              if ($type_id = array_search($key, $type_keys)) {
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
        }

        $this->view->result = $result;
        $this->view->id = $row->getIdentity();
        $this->view->msg = $this->view->translate(($result) ? 'RATE_REVIEW_EDIT_SUCCESS'
          : 'RATE_REVIEW_EDIT_ERROR');

      } else {

        // Set Form Values
        $form->offerreview_id->setValue($row->getIdentity());
        $form->title->setValue($row->title);
        $form->body->setValue($row->body);

        $this->view->form = $form;
        $this->view->html = $this->view->render('edit.tpl');
      }
    }
  }

  public function removeAction()
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
    $this->view->msg = $this->view->translate(($result) ? 'RATE_REVIEW_DELETE_SUCCESS'
      : 'RATE_REVIEW_DELETE_ERROR');

    $this->view->isAllowedPost = $tbl->isAllowedPost($offer_id, $viewer);
  }

  public function listAction()
  {
    $this->view->result = true;
    $this->view->offer_id = $offer_id = $this->_getParam('offer_id');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    if (!Engine_Api::_()->core()->hasSubject()) {
      Engine_Api::_()->core()->setSubject(Engine_Api::_()->getItem('offer', $offer_id));
    }

    $types = Engine_Api::_()->getApi('core', 'rate')->getOfferTypes($offer_id);

    // get paginator
    $tbl = Engine_Api::_()->getDbTable('offerreviews', 'rate');
    $this->view->paginator = $tbl->getPaginator($offer_id, $viewer->getIdentity(), $this->_getParam('page'));

    $this->view->isAllowedPost = $tbl->isAllowedPost($offer_id, $viewer);

    // is allowed remove
    $this->view->isAllowedRemove = Engine_Api::_()->getApi('core', 'rate')
      ->isAllowRemoveReview($offer_id, $viewer);
    $this->view->countOptions = count($types);
    $this->view->html = $this->view->render('list.tpl');
    $this->view->count = $this->view->paginator->getCurrentItemCount();
  }
}