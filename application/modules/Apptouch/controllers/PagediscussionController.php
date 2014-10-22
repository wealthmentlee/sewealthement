<?php

class Apptouch_PagediscussionController extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->page_id = $page_id = $this->_getParam('page_id');
    if (!$page_id)
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));

    $this->pageObject = $page = Engine_Api::_()->getItem('page', $page_id);
    if (!$page) {
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->viewer = Engine_Api::_()->user()->getViewer();

    $this->isAllowedView = $this->getPageApi()->isAllowedView($page);
    if (!$this->isAllowedView) {
      $this->isAllowedPost = false;
      $this->isAllowedComment = false;
      return $this->redirect($this->view->url(array('format' => 'json'), 'page_browse'));
    }

    $this->isAllowedPost = $this->getApi()->isAllowedPost($page);
    $this->isAllowedComment = $this->getPageApi()->isAllowedComment($page);

    $this->addPageInfo('contentTheme', 'd');
  }

  public function indexDeleteAction()
  {
    $topic = Engine_Api::_()->getItem('pagediscussion_pagetopic', $this->_getParam('discussion_id'));

    $form = new Engine_Form();
    $form->setTitle('PAGEDISCUSSION_DELETETOPIC_TITLE')
      ->setDescription('PAGEDISCUSSION_DELETETOPIC_DESCRIPTION');
    $form->addElement('Button', 'submit', array(
      'label' => 'Confirm',
      'type' => 'submit',
    ));

    $form->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
    ));

    $form->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    $form->getDisplayGroup('buttons');

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $topic->delete();

    return $this->redirect($this->view->url(array('page_id' => $this->pageObject->url, 'tab' => 'discussion'), 'page_view', true));
  }

  public function indexRenameAction()
  {
    $topic = Engine_Api::_()->getItem('pagediscussion_pagetopic', $this->_getParam('discussion_id'));

    $form = new Pagediscussion_Form_Rename();
    $form->removeAttrib('onsubmit');
    $form->getElement('cancel')->setAttrib('onclick', '');
    $form->title->setValue($topic->title);

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($values = $this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $topic->title = $values['title'];
    $topic->save();

    return $this->redirect($topic->getHref());
  }

  public function indexClosedAction()
  {
    $topic = Engine_Api::_()->getItem('pagediscussion_pagetopic', $this->_getParam('discussion_id'));

    if ($topic->closed)
      $topic->closed = 0;
    else
      $topic->closed = 1;

    $topic->save();

    return $this->redirect($topic->getHref());
  }

  public function indexStickyAction()
  {
    $topic = Engine_Api::_()->getItem('pagediscussion_pagetopic', $this->_getParam('discussion_id'));

    if ($topic->sticky)
      $topic->sticky = 0;
    else
      $topic->sticky = 1;

    $topic->save();

    return $this->redirect($topic->getHref());
  }

  public function indexWatchAction()
  {
    $topic = Engine_Api::_()->getItem('pagediscussion_pagetopic', $this->_getParam('discussion_id'));
    $tbl_watch = Engine_Api::_()->getDbTable('pagetopicwatches', 'pagediscussion');

    if ($topic->isWatching($this->viewer->getIdentity()))
      $watch = 0;
    else
      $watch = 1;

    $tbl_watch->setWatch(
      $this->pageObject->getIdentity(),
      $topic->getIdentity(),
      $this->viewer->getIdentity(),
      $watch
    );

    return $this->redirect($topic->getHref());
  }

  public function indexPostAction()
  {
    $topic = Engine_Api::_()->getItem('pagediscussion_pagetopic', $this->_getParam('discussion_id'));
    if (!$this->viewer || !$this->viewer->getIdentity() || !$this->isAllowedPost) {
      return $this->redirect($topic->getHref());
    }

    $result = false;

    if ($topic && !$topic->closed) {

      $form = new Pagediscussion_Form_Post;
      $isValid = ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()));

      if ($isValid) {
        $values = $form->getValues();
        $values['topic_id'] = $topic->getIdentity();
        $values['page_id'] = $topic->page_id;
        $values['user_id'] = $this->viewer->getIdentity();
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
          $tbl_watch->notifyAll($topic, $post, $this->viewer);

          // Set Watch
          $tbl_watch->setWatch(
            $topic->page_id,
            $topic->getIdentity(),
            $this->viewer->getIdentity(),
            $values['watch']
          );

          // Add Activity
          $link = $topic->getLink(array('child_id' => $post->getIdentity()));

          $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
          $action = $activityApi->addActivity($this->viewer, $topic->getParentPage(), 'page_topic_reply', null, array('is_mobile' => true, 'link' => $link));
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

    $msg = 'PAGEDISCUSSION_POST_' . (($result) ? 'SUCCESS' : 'ERROR');
    $this->view->message = $this->view->translate($msg);

    return $this->redirect($topic->getHref());
  }

  public function indexCreateTopicAction()
  {
    if (!$this->viewer || !$this->viewer->getIdentity()) {
      return $this->redirect($this->view->url(array('page_id' => $this->pageObject->url, 'format' => 'json'), 'page_view'));
    }

    $form = new Pagediscussion_Form_Create();
    $form->removeAttrib('onsubmit');

    if (!$this->getRequest()->isPost()) {
      $this->add($this->component()->subjectPhoto($this->pageObject))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->subjectPhoto($this->pageObject))
        ->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $result = false;

    $values = $form->getValues();
    $values['page_id'] = $this->page_id;
    $values['user_id'] = $this->viewer->getIdentity();
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
        $this->pageObject->getIdentity(),
        $topic->getIdentity(),
        $this->viewer->getIdentity(),
        $values['watch']
      );


      // Add Activity
      $link = $topic->getLink(array('child_id' => $post->getIdentity()));

      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($this->viewer, $this->pageObject, 'page_topic_create', null, array('is_mobile' => true, 'link' => $link));
      if ($action) {
        $activityApi->attachActivity($action, $post, Activity_Model_Action::ATTACH_DESCRIPTION);
      }

      // notify all teams
      $api = Engine_Api::_()->getDbtable('notifications', 'activity');
      $teamMembers = $this->pageObject->getAdmins();
      foreach ($teamMembers as $member) {
        if ($member->isSelf($this->viewer)) {
          continue;
        }
        $api->addNotification($member, $this->viewer, $topic, 'page_discussion_team', array(
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
      $result = true;

    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $msg = 'PAGEDISCUSSION_CREATE_' . (($result) ? 'SUCCESS' : 'ERROR');

    $this->view->msg = $this->view->translate($msg);

    return $this->redirect($topic->getHref());
  }

  public function indexDeletePostAction()
  {
    $post = Engine_Api::_()->getItem('pagediscussion_pagepost', $this->_getParam('post_id'));

    if( !$post || !$post->getIdentity()) {
      return $this->redirect($this->pageObject->getHref());
    }

    $topic = $post->getParent();

    if( !$this->viewer || !$this->viewer->getIdentity() || !($topic->isOwner($this->viewer) || $this->pageObject->isAdmin($this->viewer)) ) {
      return $this->redirect($topic->getHref());
    }

    $form = new Engine_Form();
    $form->setTitle('PAGEDISCUSSION_DELETEPOST_TITLE');
    $form->setDescription('PAGEDISCUSSION_DELETEPOST_DESCRIPTION');
    $form->addElement('Button', 'submit', array(
      'label' => 'Confirm',
      'type' => 'submit'
    ));

    if( !$this->getRequest()->isPost() ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost())) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $post->delete();

    $this->view->message = $this->view->translate('PAGEDISCUSSION_DELETEPOST_SUCCESS');

    return $this->redirect($topic->getHref());
  }

  public function indexEditPostAction()
  {
    $post = Engine_Api::_()->getItem('pagediscussion_pagepost', $this->_getParam('post_id'));

    if( !$post || !$post->getIdentity()) {
      return $this->redirect($this->pageObject->getHref());
    }

    $topic = $post->getParent();

    if( !$this->viewer || !$this->viewer->getIdentity() || !($topic->isOwner($this->viewer) || $this->pageObject->isAdmin($this->viewer)) || !$this->isAllowedPost || $topic->closed) {
      return $this->redirect($topic->getHref());
    }

    $form = new Pagediscussion_Form_Post();
    $form->setTitle('PAGEDISCUSSION_EDIT_HEADER');
    $form->removeAttrib('onsubmit');
    $form->getElement('body')->setValue($post->body);
    $form->getElement('submit')->setLabel('PAGEDISCUSSION_EDIT_SUBMIT');
    $form->getElement('cancel')->setAttrib('onclick', '')
      ->setAttrib('href', $topic->getHref())
    ;
    $form->removeElement('watch');

    if ( !$this->getRequest()->isPost() ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    try {
      $values = $form->getValues();
      $post->body = $values['body'];

      $post->save();

    } catch(Exception $e) {
      throw $e;
    }

    return $this->redirect($topic->getHref());
  }

  public function indexQuoteAction()
  {
    $post = Engine_Api::_()->getItem('pagediscussion_pagepost', $this->_getParam('post_id'));

    if( !$post || !$post->getIdentity()) {
      return $this->redirect($this->pageObject->getHref());
    }

    $topic = $post->getParent();

    if( !$this->viewer || !$this->viewer->getIdentity() || !$this->isAllowedPost ) {
      return $this->redirect($topic->getHref());
    }

    $quote = "[blockquote][b][url=" . $this->viewer->getHref() . "]" . $this->viewer->getTitle() . "[/url][/b]\n"
      . htmlspecialchars_decode($post->body)
      . "[/blockquote]\n\n";

    $form = new Pagediscussion_Form_Post();
    $form->removeAttrib('onsubmit');
    $form->getElement('cancel')->setAttrib('onclick', '')
      ->setAttrib('href', $topic->getHref())
    ;

    $form->body->setValue($quote);

    if( !$this->getRequest()->isPost() ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) ) {
      $this->add($this->component()->form($form))
        ->renderContent();
      return;
    }

    $values = $form->getValues();
    $values['topic_id'] = $topic->getIdentity();
    $values['page_id'] = $topic->page_id;
    $values['user_id'] = $this->viewer->getIdentity();
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
      $tbl_watch->notifyAll($topic, $post, $this->viewer);

      // Set Watch
      $tbl_watch->setWatch(
        $topic->page_id,
        $topic->getIdentity(),
        $this->viewer->getIdentity(),
        $values['watch']
      );

      // Add Activity
      $link = $topic->getLink(array('child_id' => $post->getIdentity()));

      $activityApi = Engine_Api::_()->getDbtable('actions', 'activity');
      $action = $activityApi->addActivity($this->viewer, $topic->getParentPage(), 'page_topic_reply', null, array('is_mobile' => true, 'link' => $link));
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

      $db->commit();

      $result = true;

    }
    catch (Exception $e)
    {
      $db->rollBack();
      throw $e;
    }

    $this->view->message = $this->view->translate('PAGEDISCUSSION_POST_SUCCESS');

    return $this->redirect($topic->getHref());

  }

  protected function getApi()
  {
    return Engine_Api::_()->getApi('core', 'pagediscussion');
  }

  protected function getPageApi()
  {
    return Engine_Api::_()->getApi('core', 'page');
  }
}