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
    
class Pagediscussion_IndexController extends Core_Controller_Action_Standard
{
  protected $_subject;
  protected $_hasSubject;
  protected $_viewer;
  protected $_hasViewer;
  protected $_isTeamMember;

  public function init()
  {
    $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('page');

    if (!$isPageEnabled) {
      $this->_forward('notfound', 'error', 'core');
      return;
    }

    // page subject
    if ($page_id = $this->_getParam('page_id')) {
      $this->_subject = Engine_Api::_()->getDbTable('pages', 'page')->findRow($page_id);
    }
    $this->_hasSubject = (bool)$this->_subject;

    $this->view->subject = $this->_subject;

    // allowed
    if ($this->view->subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($this->_subject)) {
       $this->_forward('notfound', 'error', 'core');
      return;
    }

    // viewer
    $this->_viewer = Engine_Api::_()->user()->getViewer();
    $this->_hasViewer = ($this->_viewer && $this->_viewer->getIdentity());

    // is team member
    $this->_isTeamMember = ($this->_subject && $this->_viewer->getIdentity())
        ? $this->_subject->isTeamMember($this->_viewer)
        : false;

  }

  public function indexAction()
  {
    if (!$this->_hasSubject){
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $api = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');

    $paginator = $api->getPaginator($this->_subject->getIdentity(), $this->_getParam('page', 1));
    $this->view->paginator = $paginator;

    $allowPost = Engine_Api::_()->getApi('core', 'page')->isAllowedPost($this->_subject);
    $this->view->canCreate = ($this->_hasViewer && $allowPost);

  }

  public function topicAction()
  {
    $topic_id = $this->_getParam('topic_id');
    $post_id = $this->_getParam('post_id');

    // get topic by post
    if (!$topic_id && $post_id && $postRow = $this->getPost($post_id)){
      $topic_id = $postRow->topic_id;
    }

    $topic = $this->getTopic($topic_id);
    if ($topic)
    {
      $this->view->subject = $this->_subject = Engine_Api::_()->getDbTable('pages', 'page')
          ->findRow($topic->page_id);

      if ($this->_subject && !Engine_Api::_()->getApi('core', 'page')->isAllowedView($this->_subject)) {
         $this->_forward('notfound', 'error', 'core');
        return;
      }

      // is team member
      $this->_isTeamMember = ($this->_subject && $this->_viewer->getIdentity())
          ? $this->_subject->isTeamMember($this->_viewer)
          : false;

      $this->view->hasViewer = $this->_hasViewer;
      $this->view->viewer = $this->_viewer;

      $this->view->isTeamMember = $this->_isTeamMember;
      $this->view->isWatching = ($this->_hasViewer) ? $topic->isWatching( $this->_viewer->getIdentity() ) : false;
      $this->view->isOwner = $isOnwer = ($this->_hasViewer) ? $topic->getOwner()->isSelf($this->_viewer) : false;

      if (!$isOnwer)
      {
        $topic->view_count++;
        $topic->save();
      }

      $allowPost = Engine_Api::_()->getApi('core', 'page')->isAllowedPost($this->_subject);
      $this->view->canPost = ($this->_hasViewer && $allowPost && !$topic->closed);

      $this->view->topic = $topic;
      $this->view->topic_id = $topic->getIdentity();
      $this->view->paginator = $topic->getPostPaginator($this->_getParam('page'), $this->_getParam('post_id'));

    } else
    {
      return $this->_forward('error', 'core-utility', 'mobile', array(
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('PAGEDISCUSSION_NOTFOUND')),
        'return_url'=>urldecode($this->view->url(array('action' => 'index', 'page_id' => $this->_subject->getIdentity()), 'page_discussion')),
      ));
    }

  }


  public function createAction()
  {
    if (!$this->_hasViewer || !$this->_hasSubject) {
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $this->view->form = $form = new Pagediscussion_Form_Create;

    $form->removeAttrib('onsubmit');

    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    $result = false;
    $this->view->topic_id = 0;
    $this->view->post_id = 0;

    $isValid = ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()));

    if ($isValid)
    {
      $values = $form->getValues();
      $values['page_id'] = $this->_subject->getIdentity();
      $values['user_id'] = $this->_viewer->getIdentity();
/*        $values['creation_date'] = date('Y-m-d H:i:s');
      $values['modified_date'] = date('Y-m-d H:i:s');*/

      $tbl = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');
      $tbl_post = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');
      $tbl_watch = Engine_Api::_()->getDbTable('pagetopicwatches', 'pagediscussion');

      $db = $tbl->getAdapter();
      $db->beginTransaction();

      try
      {
        // Create Topic
        $topic = $tbl->createRow($values);
        $topic->save();

        $topic_id = $topic->getIdentity();

        $values['topic_id'] = $topic_id;

        // Create Post
        $post = $tbl_post->createRow($values);

        $post->save();

        // Create Watch
        $tbl_watch->setWatch(
          $this->_subject->getIdentity(),
          $topic->getIdentity(),
          $this->_viewer->getIdentity(),
          $values['watch']
        );


        // Add Activity
        $link = $topic->getLink(array('child_id' => $post->getIdentity()));

        $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
        $action = $activityApi->addActivity($this->_viewer, $topic->getParentPage(), 'page_topic_create', null, array('link' => $link , 'is_mobile' => true));
        if ($action) {
          $activityApi->attachActivity($action, $post, Activity_Model_Action::ATTACH_DESCRIPTION);
        }

        // notify all teams
        $api = Engine_Api::_()->getDbtable('notifications', 'activity');
        $teamMembers = $this->_subject->getAdmins();
        foreach ($teamMembers as $member){
          if ($member->isSelf($this->_viewer)){ continue; }
          $api->addNotification($member, $this->_viewer, $topic, 'page_discussion_team', array(
            'message' => $this->view->BBCode($post->body)
          ));
        }

        // Add Page Search
        $pageApi = Engine_Api::_()->getDbTable('search', 'page');
        $pageApi->saveData(array(
          'object' => $topic->getType(),
          'object_id' => $topic->getIdentity(),
          'page_id' => $topic->page_id,
          'title' => $topic->getTitle(),
          'body' => $post->getDescription(),
          'photo_id' => 0
        ));
        $pageApi->saveData(array(
          'object' => $post->getType(),
          'object_id' => $post->getIdentity(),
          'page_id' => $post->page_id,
          'title' => $topic->getTitle(),
          'body' => $post->getDescription(),
          'photo_id' => 0
        ));

        $db->commit();
        $this->view->topic_id = $topic_id;
        $this->view->post_id = $post->getIdentity();
        $result = true;

      }
      catch (Exception $e)
      {
        $db->rollBack();
        throw $e;
      }
    }

    if ($result){

      $return_url = $this->view->url(array(
        'action' => 'topic',
        'topic_id' => $this->view->topic_id
      ), 'page_discussion', true);

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => $this->view->translate('PAGEDISCUSSION_CREATE_SUCCESS'),
        'return_url'=> $return_url,
      ));
    }

  }

  public function renameAction()
  {
    if (!$this->_hasViewer) {
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $this->view->form = $form = new Pagediscussion_Form_Rename;

    $form->removeAttrib('onsubmit');

    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    $result = false;

    $topic = $this->getTopic($this->_getParam('topic_id'));

    if ($topic){
      $form->title->setValue($topic->title);
    }

    if ($topic && ($this->_viewer->isOwner($topic->getOwner()) || $this->_isTeamMember))
    {
      $isValid = ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()));

      if ($isValid)
      {
        $values = $form->getValues();
/*        $values['modified_date'] = date('Y-m-d H:i:s');*/
        unset($values['topic_id']);
        $this->view->topic_id = $topic->getIdentity();

        $result = $topic->setFromArray($values)->save();

        if ($result)
        {
          // Add Page Search
          $pageApi = Engine_Api::_()->getDbTable('search', 'page');
          $pageApi->saveData(array(
            'object' => $topic->getType(),
            'object_id' => $topic->getIdentity(),
            'page_id' => $topic->page_id,
            'title' => $topic->getTitle(),
            'body' => $topic->getDescription(),
            'photo_id' => 0
          ));

          // Update Posts
          $adapter = $pageApi->getAdapter();
          $pageApi->update(
            array('title' => $topic->getTitle()),
            array(
              $adapter->quoteInto('object_id IN (?)', (array) $topic->getChildIds()),
              $adapter->quoteInto('object = ?', 'pagediscussion_pagepost', 'STRING')
            )
          );

        }

      }
    }

    if ($result){

      $return_url = $this->view->url(array(
        'action' => 'topic',
        'topic_id' => $this->view->topic_id
      ), 'page_discussion', true);

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => $this->view->translate('PAGEDISCUSSION_RENAME_SUCCESS'),
        'return_url'=> $return_url,
      ));
    }

  }

  public function postAction()
  {
    if (!$this->_hasViewer) {
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $this->view->form = $form = new Pagediscussion_Form_Post;

    $form->removeAttrib('onsubmit');

    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    $post_id = $this->_getParam('post_id');
    $post = $this->getPost($post_id);

    if ($post){
      $owner = $post->getOwner();
      $form->body->setValue("[blockquote][b][url=".$owner->getHref()."]".$owner->getTitle()."[/url][/b]\n".$post->body."[/blockquote]\n\n");
    }

    $result = false;
    $this->view->topic_id = 0;
    $this->view->post_id = 0;

    $topic = $this->getTopic($this->_getParam('topic_id'));

    if ($topic && !$topic->closed) {

      $isValid = ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()));

      if ($isValid)
      {
        $values = $form->getValues();
        $values['topic_id'] = $topic->getIdentity();
        $values['page_id'] = $topic->page_id;
        $values['user_id'] = $this->_viewer->getIdentity();
/*        $values['creation_date'] = date('Y-m-d H:i:s');
        $values['modified_date'] = date('Y-m-d H:i:s');*/

        $tbl = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');
        $tbl_post = Engine_Api::_()->getDbTable('pageposts', 'pagediscussion');
        $tbl_watch = Engine_Api::_()->getDbTable('pagetopicwatches', 'pagediscussion');

        $db = $tbl->getAdapter();
        $db->beginTransaction();

        try
        {
          // Create Post
          $post = $tbl_post->createRow($values);
          $post->save();

          // Watch
          $tbl_watch->notifyAll($topic, $post, $this->_viewer);

          // Set Watch
          $tbl_watch->setWatch(
            $topic->page_id,
            $topic->getIdentity(),
            $this->_viewer->getIdentity(),
            $values['watch']
          );

          // Add Activity
          $link = $topic->getLink(array('child_id' => $post->getIdentity()));

          $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $activityApi->addActivity($this->_viewer, $topic->getParentPage(), 'page_topic_reply', null, array('link' => $link, 'is_mobile' => true));
          if ($action) {
            $activityApi->attachActivity($action, $post, Activity_Model_Action::ATTACH_DESCRIPTION);
          }

          // Add Page Search
          $pageApi = Engine_Api::_()->getDbTable('search', 'page');
          $pageApi->saveData(array(
            'object' => $post->getType(),
            'object_id' => $post->getIdentity(),
            'page_id' => $topic->page_id,
            'title' => $topic->getTitle(),
            'body' => $post->getDescription(),
            'photo_id' => 0
          ));

          $this->view->topic_id = $topic->getIdentity();
          $this->view->post_id = $post->getIdentity();
          $db->commit();

          $result = true;

        }
        catch (Exception $e)
        {
          $db->rollBack();
          throw $e;
        }
      }
    }
    if ($result){

      $return_url = $this->view->url(array(
        'action' => 'topic',
        'post_id' => $this->view->post_id
      ), 'page_discussion', true);

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => $this->view->translate('PAGEDISCUSSION_POST_SUCCESS'),
        'return_url'=> $return_url,
      ));
    }

  }

  public function editAction()
  {
    if (!$this->_hasViewer) {
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $this->view->form = $form = new Pagediscussion_Form_Edit;

    $form->removeAttrib('onsubmit');

    $element = $form->getElement('cancel');
    if ($element){
      $element->setAttribs(array(
        'href' => urldecode($this->_getParam('return_url')),
        'onclick' => ''
      ));
    }

    $result = false;
    $this->view->topic_id = 0;
    $this->view->post_id = 0;

    $post = $this->getPost($this->_getParam('post_id'));

    if ($post){
      $form->body->setValue($post->body);
    }

    if ($post && ($post->isOwner($this->_viewer) || $this->_isTeamMember))
    {
      $request = $this->getRequest();
      if (!$request->isPost() || !$form->isValid($request->getPost())) { return ; }

      $values = $form->getValues();
      $values['modified_date'] = date('Y-m-d H:i:s');
      unset($values['post_id']);

      $this->view->topic_id = $post->topic_id;
      $this->view->post_id = $post->getIdentity();
      $result = $post->setFromArray($values)->save();

      if ($result)
      {
        // Add Page Search
        $pageApi = Engine_Api::_()->getDbTable('search', 'page');
        $pageApi->saveData(array(
          'object' => $post->getType(),
          'object_id' => $post->getIdentity(),
          'page_id' => $post->page_id,
          'title' => $post->getTitle(),
          'body' => $post->getDescription(),
          'photo_id' => 0
        ));

        if ($post->isFirstPost())
        {
          $select = $pageApi->select()
              ->where('object = ?', 'pagediscussion_pagetopic', 'STRING')
              ->where('object_id = ?', $post->topic_id);
          $searchRow = $pageApi->fetchRow($select);
          if ($searchRow){
            $searchRow->body = $post->getDescription();
            $searchRow->save();
          }

        }

      }

    }

    if ($result){

      $return_url = $this->view->url(array(
        'action' => 'topic',
        'post_id' => $this->view->post_id
      ), 'page_discussion', true);

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => $this->view->translate('PAGEDISCUSSION_EDIT_SUCCESS'),
        'return_url'=> $return_url,
      ));
    }
  }


  public function deleteTopicAction()
  {
    if (!$this->_hasViewer) {
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $topic_id = (int)$this->_getParam('topic_id');
    $page_id = false;

    $this->view->form = $form = new Engine_Form;

    $form->setTitle('PAGEDISCUSSION_DELETETOPIC_TITLE')
      ->setDescription('PAGEDISCUSSION_DELETETOPIC_DESCRIPTION')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');

    $form->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => urldecode($this->_getParam('return_url')),
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');

    $form->setAction($this->view->url(array(
      'action' => 'delete-topic',
      'topic_id' => $topic_id,
      'return_url' => $this->_getParam('return_url')
    ), 'page_discussion'));

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $tbl = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');

    if (!$topic = $this->getTopic($this->_getParam('topic_id'))){
      return ;
    }
    if (!$this->_viewer->isSelf($topic->getOwner()) && !$this->_isTeamMember){
      return ;
    }

    $page_id = $topic->page_id;

    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try {
      $topic->delete();
      $db->commit();
      $result = true;
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    if ($result){

      $return_url = $this->view->url(array(
        'action' => 'index',
        'page_id' => $page_id
      ), 'page_discussion', true);

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => $this->view->translate('PAGEDISCUSSION_DELETETOPIC_SUCCESS'),
        'return_url'=>$return_url,
      ));
    }

  }

  public function deletePostAction()
  {
    if (!$this->_hasViewer) {
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $topic_id = false;
    $page_id = false;

    $this->view->form = $form = new Engine_Form;

    $form->setTitle('PAGEDISCUSSION_DELETEPOST_TITLE')
      ->setDescription('PAGEDISCUSSION_DELETEPOST_DESCRIPTION')
      ->setAttrib('class', 'global_form_popup')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setMethod('POST');

    $form->addElement('Button', 'submit', array(
      'label' => 'Delete',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => urldecode($this->_getParam('return_url')),
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }
    $tbl = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');

    if (!$post = $this->getPost((int)$this->_getParam('post_id'))){
      return ;
    }

    if (!$this->_viewer->isSelf($post->getOwner()) && !$this->_isTeamMember){
      return ;
    }
    $page_id = $post->getParent()->page_id;
    $topic_id = $post->topic_id;

    $db = $tbl->getAdapter();
    $db->beginTransaction();

    try {
      $post->delete();
      $db->commit();
      $result = true;
    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    if ($result){

      if ($this->getTopic($topic_id)){
        $return_url = $this->view->url(array(
          'action' => 'topic',
          'topic_id' => $topic_id
        ), 'page_discussion', true);
      } else {
        $return_url = $this->view->url(array(
          'action' => 'index',
          'page_id' => $page_id
        ), 'page_discussion', true);
      }

      return $this->_forward('success', 'utility', 'mobile', array(
        'messages' => $this->view->translate('PAGEDISCUSSION_DELETEPOST_SUCCESS'),
        'return_url'=>$return_url,
      ));
    }

  }


  public function discussionAction()
  {
    if (!$this->_hasViewer) {
      return $this->_redirect($this->view->url(array(array()), 'page_browse'));
    }

    $task = $this->_getParam('task');
    $set = ($this->_getParam('set')) ? 1 : 0;

    $tbl = Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion');

    $db = $tbl->getAdapter();
    $db->beginTransaction();

    $topic_id = false;
    $result = false;

    try {

      if ($topic = $this->getTopic($this->_getParam('topic_id')))
      {
        $subject = $topic->getParent();
        $isTeamMember = $subject->isTeamMember(Engine_Api::_()->user()->getViewer());

        if ($task == 'watch')
        {
          $result = Engine_Api::_()->getDbTable('pagetopicwatches', 'pagediscussion')
              ->setWatch($topic->page_id, $topic->getIdentity(), $this->_viewer->getIdentity(), $set);
        }
        else if ($task == 'close' && $isTeamMember)
        {
          $topic->closed = $set;
          $result = $topic->save();
        }
        else if ($task == 'sticky' && $isTeamMember)
        {
          $topic->sticky = $set;
          $result = $topic->save();
        }
        $topic_id = $topic->getIdentity();
      }

      $db->commit();

    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->topic_id = $topic_id;

    $msg = 'PAGEDISCUSSION_' . strtoupper($task) . '_SUCCESS';

    $return_url = $this->view->url(array(
      'action' => 'topic',
      'topic_id' => $topic_id
    ), 'page_discussion', true);

    return $this->_forward('success', 'utility', 'mobile', array(
      'messages' => $this->view->translate($msg),
      'return_url'=> $return_url,
    ));

  }


  protected function getTopic($topic_id)
  {
    if ($topic_id) {
      return Engine_Api::_()->getDbTable('pagetopics', 'pagediscussion')->findRow($topic_id);
    }
    return false;
  }

  protected function getPost($post_id)
  {
    if ($post_id) {
      return Engine_Api::_()->getDbTable('pageposts', 'pagediscussion')->findRow($post_id);
    }
    return false;
  }


}