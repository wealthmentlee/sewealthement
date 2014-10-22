<?php

class Apptouch_HequestionController
  extends Apptouch_Controller_Action_Bridge
{
  public function indexInit()
  {
    $this->addPageInfo('contentTheme', 'd');
  }

  public function indexIndexAction()
  {

    if( !$this->_helper->requireAuth()->setAuthParams('hequestion', null, 'view')->isValid() ) return;

        // Prepare data
    $viewer = Engine_Api::_()->user()->getViewer();
    $form = new Apptouch_Form_Search();

    $form->removeElement('show');

    // Process form
    $form->isValid($this->_getAllParams());
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();

    $this->view->assign($values);

    $viewer = Engine_Api::_()->user()->getViewer();

    $select = Engine_Api::_()->getDbTable('questions', 'hequestion')->getRecentQuestionSelect();

    if (!empty($values['search'])){
      $select->where('title LIKE ?', '%'.$values['search'].'%');
    }

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage( Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hequestion.perpage', 10) );
    $paginator->setCurrentPageNumber( $this->_getParam('page') );

    $this->setFormat('browse')
      ->add($this->component()->itemSearch($form));
    if ($paginator->getTotalItemCount()) {
      $this->add($this->component()->itemList($paginator, "browseItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($this->view->search) {
      $this->add($this->component()->tip(
        $this->view->translate('APPTOUCH_Nobody has created a question with that criteria.')
      ));
    } else {

      $this->add($this->component()->tip(
        $this->view->translate('HEQUESTION_Nobody has created a question yet.')
      ));

    }
    $this->renderContent();


  }

  public function indexManageAction()
  {


        // Prepare data
    $viewer = Engine_Api::_()->user()->getViewer();
    $form = new Apptouch_Form_Search();

    $form->removeElement('show');

    // Process form
    $form->isValid($this->_getAllParams());
    $values = $form->getValues();
    $values['user_id'] = $viewer->getIdentity();

    $this->view->assign($values);




    $viewer = Engine_Api::_()->user()->getViewer();

    $table = Engine_Api::_()->getDbTable('questions', 'hequestion');

    $select = $table->select()
        ->where('user_id = ?', $viewer->getIdentity())
        ->order('creation_date DESC');

    if (!empty($values['search'])){
      $select->where('title LIKE ?', '%'.$values['search'].'%');
    }




    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage( Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hequestion.perpage', 10) );
    $paginator->setCurrentPageNumber( $this->_getParam('page') );

    $this->view->paginator = $paginator;



    $this->setFormat('manage')
      ->add($this->component()->itemSearch($form));
    if ($paginator->getTotalItemCount()) {
      $this->add($this->component()->itemList($paginator, "manageItemData", array('listPaginator' => true,)))
//        ->add($this->component()->paginator($paginator))
      ;
    } elseif ($this->view->category || $this->view->search) {
      $this->add($this->component()->tip(
        $this->view->translate('APPTOUCH_Nobody has created a question with that criteria.')
      ));
    } else {
        $this->add($this->component()->tip(
          $this->view->translate('HEQUESTION_NO_USER_QUESTIONS')
        ));
    }
    $this->renderContent();


  }


  public function browseItemData(Core_Model_Item_Abstract $item)
  {
    $owner = $item->getOwner();
    $customize_fields = array(
      'descriptions' => array(

          $this->view->translate(array('%s vote', '%s votes', $item->vote_count), $item->vote_count) . ' '
            . ' <span>&middot;</span>'. ' '
            . $this->view->translate(array('%s follower', '%s followers', $item->follower_count), $item->follower_count) . ' '
            . ' <span>&middot;</span>'. ' '
            . $this->view->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())

      ),
      'photo' => $owner->getPhotoUrl('thumb.normal'),
    );
    return $customize_fields;
  }


  public function manageItemData(Core_Model_Item_Abstract $item)
  {
    $owner = $item->getOwner();
    $customize_fields = array(
      'descriptions' => array(

        $this->view->translate(array('%s vote', '%s votes', $item->vote_count), $item->vote_count) . ' '
          . ' <span>&middot;</span>'. ' '
          . $this->view->translate(array('%s follower', '%s followers', $item->follower_count), $item->follower_count) . ' '
          . ' <span>&middot;</span>'. ' '
          . $this->view->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle())

      ),
      'photo' => $owner->getPhotoUrl('thumb.normal'),
    );
    return $customize_fields;
  }

  public function indexViewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if ($question){
      Engine_Api::_()->core()->setSubject($question);
      $this->view->question = $question;
      $this->view->show_all = true;
    }

    if (!$this->_helper->requireSubject()->isValid()){
      return;
    }
    if (!$this->_helper->requireAuth()->setAuthParams($question, $viewer, 'view')->isValid()){
      return;
    }


    $owner = $question->getOwner();
    $subject = $this->subject($owner);

    $this->setFormat('view')
      ->add($this->component()->quickLinks('gutter'))
      ->add($this->component()->date(array('title' => $this->view->translate('Posted by') . ' ' . $owner->getTitle() . ' ' . $this->view->timestamp($question->creation_date), 'count' => null)))
      ->question($question)
      ->renderContent();

  }


  protected function question(Hequestion_Model_Question $question)
  {

    $viewer = Engine_Api::_()->user()->getViewer();

    $owner = $question->getOwner();

    $this
      ->add($this->component()->html($this->dom()->new_('h3', array(), $question->getTitle())));


    $paginator = $question->getOptionPaginator();
    $paginator->setItemCountPerPage(100);


    $max = 0;

    foreach ($paginator as $option) {
      if ($option->vote_count > $max) {
        $max = $option->vote_count;
      }
    }

    $options = array();
    foreach ($paginator as $option) {
      $is_active = ($option->getVote($viewer)) ? 1 : 0;
      if ($is_active) {
        $voted = true;
      }
      $option_percent = @floor($option->vote_count / $max * 100);

      $elms = array();

      $input = null;
      if ($question->canVote($viewer)) {
        if ($question->isMulti()) {
          $input = $this->dom()->new_('input', array(
            'type' => 'checkbox',
            'data-id' => $option->getIdentity(),
            'name' => 'hqchecked_' . $option->option_id,
            'value' => $option->option_id,
            'class' => 'hqchecked_' . $option->option_id,
            'data-role' => 'none',
            'data-url' => $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'vote', 'question_id' => $question->getIdentity(), 'option_id' => $option->getIdentity(), 'format' => 'json'), 'default', true),
          ));
        } else {
          $input = $this->dom()->new_('input', array(
            'type' => 'radio',
            'data-id' => $option->getIdentity(),
            'name' => 'hqradio',
            'value' => $option->option_id,
            'class' => 'hqselected_' . $option->option_id,
            'data-role' => 'none',
            'data-url' => $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'vote', 'question_id' => $question->getIdentity(), 'option_id' => $option->getIdentity(), 'format' => 'json'), 'default', true),
          ));
        }

        if ($is_active) {
          $input->attr('checked', 'checked');
        }

      }


      $elms[] = $this->dom()->new_('div', array('class' => 'hqContentCountLine', 'style' => 'width:' . $option_percent . '%'));
      $elms[] = $this->dom()->new_('div', array('class' => 'hqContentTitle'), $this->dom()->new_('a', array('href' => 'javascript:void(0);'), $option->getTitle()));

      $front_router = Zend_Controller_Front::getInstance()->getRouter();

      $href = $front_router->assemble(array(
        'module' => 'hecore',
        'controller' => 'index',
        'action' => 'list',
      ), 'default', true);

      $query = http_build_query(array(
        'm' => 'hequestion',
        'l' => 'getQuestionVoters',
        'params' => array(
          'question_id' => $question->getIdentity(),
          'option_id' => $option->getIdentity(),
        )
      ));

      $href .= '?' . $query;

      $elms[] = $this->dom()->new_('div', array('class' => 'hqContentVotes'), $this->dom()->new_('a', array('href' => $href), $this->view->translate(array('%s vote', '%s votes', $option->vote_count), $option->vote_count)));

      $options[] = $this->dom()->new_('li', array(), '', array(
        $this->dom()->new_('div', array('class' => 'hqUserChoose'), $this->dom()->new_('div', array('class' => 'hqUserChooseControl'), $input)),
        $this->dom()->new_('div', array('class' => 'hqContent'), '', $elms)
      ));

    }


    $html = $this->dom()->new_('ul', array(
      'class' => 'hqView',
    ), '', $options);


    $this->add($this->component()->html($html));


    if ($question->can_add && $viewer->getIdentity()) {

      $html = $this->dom()->new_('div', array('class' => 'hqAddAnswer'), null, array(
        $this->dom()->new_('input', array(
          'placeholder' => $this->view->translate('HEQUESTION_ADD_ANSWER'),
          'class' => 'hqAnswerBody'
        )),
        $this->dom()->new_('button', array(
          'type' => 'button',
          'data-url' => $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'add-answer', 'question_id' => $question->getIdentity(), 'format' => 'json'), 'default', true),
          'class' => 'hqAnswerButton'
        ), $this->view->translate('HEQUESTION_ADD_ANWER_SUBMIT'))
      ));
      $this->add($this->component()->html($html));
    }


    $isFollower = $question->isFollower($viewer);


    $router = Zend_Controller_Front::getInstance()->getRouter();

    $paramStr = '?m=suggest&c=' . $this->view->url(array(
      "module" => 'hequestion',
      "controller" => "index",
      "action" => "ask-friends",
      "question_id" => $question->getIdentity()
    ), "default", true) . '&m=hequestion&l=getFriends&nli=0&params[question_id]=' . $question->getIdentity() .
      '&action_url=' . urlencode($question->getHref());

    $url = $router->assemble(array('controller' => 'index', 'action' => 'contacts', 'module' => 'hecore'), 'default', true) . $paramStr;


    $options = array();


    if ($viewer->getIdentity()) {

      // Ask friends
      $options[] = $this->dom()->new_('li', array('data-icon' => 'question'), $this->dom()->new_('a', array('href' => $url), $this->view->translate('HEQUESTION_ASK_FRIENDS')));


      // Follow

      $options[] = $this->dom()->new_('li', array('data-icon' => 'heart',
          'style' => (($isFollower) ? 'display:none;' : ''),
          'class' => 'hqFollow'
        ),
        $this->dom()->new_('a', array(
          'data-icon' => 'heart',
          'data-url' => $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'follow', 'format' => 'json'), 'default', true)
        ), $this->view->translate('HEQUESTION_FOLLOW')));


      $options[] = $this->dom()->new_('li', array(
          'style' => (($isFollower) ? '' : 'display:none;'),
          'class' => 'hqUnfollow'
        ),
        $this->dom()->new_('a', array(
          'data-icon' => 'heart',
          'data-url' => $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'unfollow', 'format' => 'json'), 'default', true)
        ), $this->view->translate('HEQUESTION_UNFOLLOW')));


      if ($question->hasVote($viewer)) {
        $url = $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'unvote', 'question_id' => $question->getIdentity(), 'format' => 'json'), 'default', true);
        $options[] = $this->dom()->new_('li', array('data-icon' => 'delete'), $this->dom()->new_('a', array('href' => 'javascript:void(0);', 'class' => 'hqUnvote', 'data-url' => $url), $this->view->translate('HEQUESTION_UNVOTE')));
      }

      $url = $this->view->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $question->getType(), 'id' => $question->getIdentity()), 'default', true);
      $options[] = $this->dom()->new_('li', array('data-icon' => 'plus'), $this->dom()->new_('a', array('href' => $url), $this->view->translate('Share')));

      if (!$question->isOwner($viewer)) {
        $url = $this->view->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $question->getGuid()), 'default', true);
        $options[] = $this->dom()->new_('li', array('data-icon' => 'flag'), $this->dom()->new_('a', array('href' => $url), $this->view->translate('Report')));
      }

      /**
      if ($question->authorization()->isAllowed($viewer, 'edit')){
      $url = $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'edit'), 'default', true);
      $options[] = $this->dom()->new_('li', array('data-icon' => 'edit'), $this->dom()->new_('a', array('href' => $url), $this->view->translate('HEQUESTIONS_Edit options')));
      }
       */


      if ($question->authorization()->isAllowed($viewer, 'delete')) {
        $url = $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'delete', 'format' => 'json', 'question_id' => $question->getIdentity()), 'default', true);

        $options[] = $this->dom()->new_('li', array('data-icon' => 'delete'), $this->dom()->new_('a', array(
          'href' => 'javascript:void(0)',
          'data-url' => $url,
          'data-message' => $this->view->translate("HEQUESTION_DELETE_TITLE"),
          'data-url-browse' => $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'index'), 'default', true),
          'class' => 'hqDelete'
        ), $this->view->translate('Delete')));
      }

    }


    $auth = Engine_Api::_()->authorization()->context;

    $availableLabels = array(
      'everyone' => 'HEQUESTION_Everyone',
      'owner_network' => 'HEQUESTION_Friends and Networks',
      'owner_member' => 'HEQUESTION_Friends Only',
      'owner' => 'HEQUESTION_Just Me'
    );

    $viewOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('hequestion', $viewer, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    $privacy_active = '';
    $privacy_active_key = '';

    if (!empty($viewOptions)) {
      $keys = array_keys($viewOptions);
      $privacy_active_key = $keys[0];
      $privacy_active = $viewOptions[$privacy_active_key];
      foreach (array_reverse(array_keys($viewOptions)) as $role) {
        if (1 === $auth->isAllowed($question, $role, 'view')) {
          $privacy_active_key = $role;
          $privacy_active = $viewOptions[$privacy_active_key];
        }
      }
    }

    $privacy = array();

    if (!empty($viewOptions)) {
      foreach ($viewOptions as $key => $item) {
        $privacy[] = array(
          'type' => $key,
          'active' => ($key == $privacy_active_key),
          'label' => $this->view->translate($item)
        );
      }
    }


    if ($question->authorization()->isAllowed($viewer, 'edit') && !empty($privacy)) {
      $options[] = $this->dom()->new_('li', array('data-icon' => 'lock'), $this->dom()->new_('a', array('href' => 'javascript:void(0);', 'class' => 'hqPrivacy'), $this->view->translate('Privacy')));
    }


    $html = '';
    if (!empty($options)) {
      $html = $this->dom()->new_('ul', array(
        'data-role' => 'listview',
        'data-inset' => true,
        'data-mini' => 'true'
      ), '', $options);
    }

    if (!empty($html)){
      $this->add($this->component()->html($html));
    }


    $options = array();
    if (!empty($privacy)) {

      $url = $this->view->url(array('module' => 'hequestion', 'controller' => 'index', 'action' => 'privacy', 'question_id' => $question->getIdentity(), 'format' => 'json'), 'default', true);

      foreach ($privacy as $item) {

        $input = $this->dom()->new_('input', array(
          'id' => 'privacy_' . $item['type'],
          'name' => 'privacy',
          'type' => 'radio',
          'value' => $item['type'],
          'data-url' => $url,
          'class' => 'hqPrivacyRadio'
        ));
        if (!empty($item['active'])) {
          $input->attr('checked', 'checked');
        }
        $label = $this->dom()->new_('label', array('for' => 'privacy_' . $item['type']), $item['label']);
        $options[] = $input . '' . $label;
      }
    }

    $html = $this->dom()->new_('fieldset', array(
      'data-role' => 'controlgroup',
      'data-mini' => 'true',
      'style' => 'display:none',
      'class' => 'hqPrivacyForm'
    ), '', $options);


    $this->add($this->component()->html($html));


    return $this;
  }


  public function indexAskFriendsAction()
  {

    $uids = $this->_getParam('uids', array());

    $this->view->message = $this->view->translate('HEQUESTION_ASKED_ERROR');

    $viewer = Engine_Api::_()->user()->getViewer();

    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));
    if ($question){
      Engine_Api::_()->core()->setSubject($question);
    }


    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) {
      return;
    }
    if (!$question){
      return ;
    }


    $users = Engine_Api::_()->getItemMulti('user', $uids);
    if (!empty($users)){

      $notifyTable = Engine_Api::_()->getDbTable('notifications', 'activity');

      foreach ($users as $user){

        $oldNotify = $notifyTable->fetchRow(array(
          'user_id = ?' => $user->getIdentity(),
          'object_type = ?' => $viewer->getType(),
          'object_id = ?' => $viewer->getIdentity(),
          'type = ?' => 'hequestion_ask'
        ));

        if (!$oldNotify){
          $notifyTable->addNotification($user, $viewer, $question, 'hequestion_ask');
        }

      }

    }

    $this->view->result = true;
    $this->view->message = $this->view->translate('HEQUESTION_ASKED_SUCCESS');

    if ($question){
      $this->redirect($question->getHref());
    }

  }


  public function indexFollowAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if ($viewer->getIdentity() && $question){
      Engine_Api::_()->getDbTable('followers', 'hequestion')->follow($viewer, $question);
    }

  }

  public function indexUnfollowAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if ($viewer->getIdentity() && $question){
      Engine_Api::_()->getDbTable('followers', 'hequestion')->unfollow($viewer, $question);
    }

  }



  public function indexCreateAction()
  {
    $this->view->message = $this->view->translate('HEQUESTION_ERROR');



    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams('hequestion', null, 'create')->isValid() ) {
      return;
    }



    $this->view->form = $form = new Hequestion_Form_Create();
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }


    $options = $this->getRequest()->getPost('options');
    $form_options = array();

    $option_count = 0;
    if (!empty($options)){
      foreach ($options as $key => $option){

        // limit
        if ($option_count >= (int) Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hequestion.maxoptions', 15)){
          break ;
        }

        $optionForm = new Hequestion_Form_Option();
        if ($optionForm->isValid(array('title' => $option))){
          $optionValues = $optionForm->getValues();
          $form_options[$key] = $optionValues['title'];
          $option_count++;
        }
      }
    }

    $values = $form->getValues();

    if (!$values['can_add'] && (empty($form_options) || count($form_options) < 2)){
      $this->view->message = $this->view->translate('HEQUESTION_ERROR_LEAST_OPTIONS');
      return ;
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {

      $values['user_id'] = $viewer->getIdentity();
      $values['owner_type'] = $viewer->getType();
      $values['owner_id'] = $viewer->getIdentity();

      $subject = $viewer;
      if ($this->_getParam('subject')){
        $subject = Engine_Api::_()->getItemByGuid($this->_getParam('subject'));
        if ($subject){
          $values['parent_type'] = $subject->getType();
          $values['parent_id'] = $subject->getIdentity();
        }
      }
      $type = 'hequestion_ask';
      if( $viewer->isSelf($subject) ) {
        $type = 'hequestion_ask_self';
      }


      $table = Engine_Api::_()->getDbTable('questions', 'hequestion');

      $question = $table->createRow();
      $question->setFromArray($values);
      $question->save();


      $optionTable = Engine_Api::_()->getDbTable('options', 'hequestion');

      foreach ($form_options as $item)
      {
        $option = $optionTable->createRow();
        $option->setFromArray(array(
          'title' => $item,
          'user_id' => $viewer->getIdentity(),
          'question_id' => $question->getIdentity()
        ));
        $option->save();
      }


      // Privacy
      $auth = Engine_Api::_()->authorization()->context;
      $roles = array('owner', 'owner_member', 'owner_network', 'everyone');

      if( empty($values['auth_view']) ) {
        $values['auth_view'] = 'everyone';
      }
      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = 'everyone';
      }


      $viewMax = array_search($values['auth_view'], $roles);
      $commentMax = array_search($values['auth_comment'], $roles);

      foreach( $roles as $i => $role ) {
        $auth->setAllowed($question, $role, 'view', ($i <= $viewMax));
        $auth->setAllowed($question, $role, 'comment', ($i <= $commentMax));
      }

      $auth->setAllowed($question, 'registered', 'vote', true);




      // Add on activity feed

      $actionTable = Engine_Api::_()->getDbTable('actions', 'wall');
      $action = $actionTable->addActivity($viewer, $subject, $type, null, array('is_mobile' => true,
        'question' => '<a href="'.$question->getHref().'">'.$question->getTitle().'</a>'
      ));

      if ($action){
        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $question);
      }

      if ($action) {
        $this->view->action = $this->getHelper('activity')->direct()->activity($action, array());
        $this->view->last_id = $action->getIdentity();
        $this->view->last_date = $action->date;
      }


      $db->commit();

      $this->view->status = true;


    } catch (Exception $e){
      $db->rollBack();
      throw $e;
    }


  }




  public function indexDeleteAction()
  {
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if( !$this->_helper->requireAuth()->setAuthParams($question, null, 'delete')->isValid()) return;

    if (!$this->getRequest()->isPost()){
      return ;
    }

    $question->delete();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your question has been deleted.');

  }


  public function indexPrivacyAction()
  {

    $viewer = Engine_Api::_()->user()->getViewer();
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if ($question){
      Engine_Api::_()->core()->setSubject($question);
      $this->view->question = $question;
    }


    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }


    $availableLabels = array(
      'everyone'            => 'HEQUESTION_Everyone',
      'owner_network'       => 'HEQUESTION_Friends and Networks',
      'owner_member'        => 'HEQUESTION_Friends Only',
      'owner'               => 'HEQUESTION_Just Me'
    );

    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('hequestion', $viewer, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));


    $values = array();

    $values['auth_view'] = $this->_getParam('privacy');
    if (empty($viewOptions[$values['auth_view']])){
      die('Ops...');
    }


    // Privacy
    $auth = Engine_Api::_()->authorization()->context;
    $roles = array('owner', 'owner_member', 'owner_network', 'everyone');

    if( empty($values['auth_view']) ) {
      $values['auth_view'] = 'everyone';
    }
    if( empty($values['auth_comment']) ) {
      $values['auth_comment'] = 'everyone';
    }

    $viewMax = array_search($values['auth_view'], $roles);
    $commentMax = array_search($values['auth_comment'], $roles);

    foreach( $roles as $i => $role ) {
      $auth->setAllowed($question, $role, 'view', ($i <= $viewMax));
      $auth->setAllowed($question, $role, 'comment', ($i <= $commentMax));
    }

    $auth->setAllowed($question, 'registered', 'vote', true);




    $actionTable = Engine_Api::_()->getDbtable('actions', 'activity');
    foreach( $actionTable->getActionsByObject($question) as $action ) {
      $actionTable->resetActivityBindings($action);
    }


    $this->view->result = true;

  }



  public function indexDeleteOptionAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if ($question){
      Engine_Api::_()->core()->setSubject($question);
      $this->view->question = $question;
    }

    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'edit')->isValid() ) {
      return;
    }


    $option = $question->getOption($this->_getParam('option_id'));
    if (!$option){
      return ;
    }



    $optionTable = Engine_Api::_()->getDbTable('options', 'hequestion');

    $select = $optionTable->select()
        ->where('question_id = ?', $question->getIdentity())
        ->where('option_id != ?', $option->option_id)
        ->order('vote_count DESC');

    $paginator = Zend_Paginator::factory($select);



    if (!$question->can_add && (!$paginator->getTotalItemCount() || $paginator->getTotalItemCount() < 2)){
      $this->view->message = $this->view->translate('HEQUESTION_ERROR_LEAST_OPTIONS');
      return ;
    }


    $option->delete();


    // vote_count
    $question->refresh();


    $this->view->body = $this->question($question);

  }



  public function indexVoteAction()
  {

    $this->view->status = false;

    $viewer = Engine_Api::_()->user()->getViewer();

    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));
    if ($question){
      Engine_Api::_()->core()->setSubject($question);
    }


    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) {
      return;
    }
    if (!$question){
      return ;
    }
    if (!$question->canVote($viewer)){
      return ;
    }


    $option = $question->getOption($this->_getParam('option_id'));
    $vote = $this->_getParam('vote', true);

    if (!$option){
      return ;
    }

    if ($vote){

      $option->vote($viewer);
      $this->addVoteNotifications($question, $viewer);

    } else {
      $option->unvote($viewer);
    }



    $actionTable = Engine_Api::_()->getDbTable('actions', 'wall');

    $action = $actionTable->fetchRow(array(
      'object_id = ?' => $question->getIdentity(),
      'object_type = ?' => $question->getType(),
      'subject_id = ?' => $viewer->getIdentity(),
      'subject_type = ?' => $viewer->getType(),
      'type = ?' => 'hequestion_answer'
    ));


    if (count($question->getObjectAnswers($viewer))){
      if (!$action){
        $action = $actionTable->addActivity($viewer, $question, 'hequestion_answer', null, array('is_mobile' => true));
      }
      if ($action){
        $body = $question->getObjectAnswersBody($viewer);
        $action->body = $body;
        $action->save();
      }
    } else {
      if ($action){
        $action->delete();
      }
    }


    // vote_count
    $question->refresh();

    $this->view->status = true;


  }


  public function indexUnvoteAction()
  {
    $this->view->status = false;

    $viewer = Engine_Api::_()->user()->getViewer();

    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));
    if ($question){
      Engine_Api::_()->core()->setSubject($question);
    }


    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireAuth()->setAuthParams(null, null, 'view')->isValid() ) {
      return;
    }
    if (!$question){
      return ;
    }
    if (!$question->canVote($viewer)){
      return ;
    }


    $question->unvote($viewer);


    // vote_count
    $question->refresh();


    $this->view->status = true;



  }


  public function indexAddAnswerAction()
  {
    $this->view->status = false;

    $viewer = Engine_Api::_()->user()->getViewer();
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if (!$question){
      return ;
    }

    // limit
    if ($question->getOptionPaginator()->getTotalItemCount() >= (int) Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hequestion.maxoptions', 15)){
      $this->view->message = $this->view->translate('HEQUESTION_MAX_OPTIONS');
      return ;
    }

    Engine_Api::_()->core()->setSubject($question);

    $form = new Hequestion_Form_Option();

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $values = $form->getValues();


    $optionTable = Engine_Api::_()->getDbTable('options', 'hequestion');

    $option = $optionTable->createRow();
    $option->setFromArray($values);
    $option->setFromArray(array(
      'user_id' => $viewer->getIdentity(),
      'question_id' => $question->getIdentity()
    ));
    $option->save();


    if (!$question->isOwner($viewer)){
      $option->vote($viewer);
      $this->addVoteNotifications($question, $viewer);
    }



    $this->view->status = true;
    $this->view->body = $this->question($question);


  }



  public function indexRemoveLinkAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if ($question){
      Engine_Api::_()->core()->setSubject($question);
      $this->view->question = $question;
    }


    if( !$this->_helper->requireUser()->isValid() ) {
      return;
    }
    if( !$this->_helper->requireSubject()->isValid() ) {
      return;
    }

    if (!$question->canRemoveLink($viewer)){
      return ;
    }

    $question->parent_type = '';
    $question->parent_id = 0;
    $question->save();

  }


  public function addVoteNotifications($question, $viewer)
  {

    // Add notification to owner

    if (!$question->isOwner($viewer)){

      $notifyTable = Engine_Api::_()->getDbTable('notifications', 'activity');

      $oldNotify = $notifyTable->fetchRow(array(
        'user_id = ?' => $question->getOwner()->getIdentity(),
        'subject_type = ?' => $viewer->getType(),
        'subject_id = ?' => $viewer->getIdentity(),
        'object_type = ?' => $question->getType(),
        'object_id = ?' => $question->getIdentity(),
        'type = ?' => 'hequestion_answer'
      ));

      if (!$oldNotify){
        $notifyTable->addNotification($question->getOwner(), $viewer, $question, 'hequestion_answer');
      }

    }


    // Add notifications to followers

    foreach ($question->getFollowers() as $follower)
    {
      $userFollower = Engine_Api::_()->getItem('user', $follower->user_id);
      if (!$userFollower){
        continue ;
      }
      if ($question->isOwner($userFollower)){
        continue ;
      }
      if ($viewer->isSelf($userFollower)){
        continue ;
      }

      $notifyTable = Engine_Api::_()->getDbTable('notifications', 'activity');

      $oldNotify = $notifyTable->fetchRow(array(
        'user_id = ?' => $userFollower->getIdentity(),
        'subject_type = ?' => $viewer->getType(),
        'subject_id = ?' => $viewer->getIdentity(),
        'object_type = ?' => $question->getType(),
        'object_id = ?' => $question->getIdentity(),
        'type = ?' => 'hequestion_follow'
      ));

      if (!$oldNotify){
        $notifyTable->addNotification($userFollower, $viewer, $question, 'hequestion_follow');
      }

    }
  }




}