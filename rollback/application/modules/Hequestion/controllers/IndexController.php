<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hequestion_IndexController extends Core_Controller_Action_Standard
{

  protected $_script_module;

  public function init()
  {
    $this->_script_module = ($this->_getParam('is_timeline', false))? 'timeline':'wall';
  }

  public function indexAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $paginator = Engine_Api::_()->getDbTable('questions', 'hequestion')->getQuestionsPaginator();
    $paginator->setItemCountPerPage( Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hequestion.perpage', 10) );
    $paginator->setCurrentPageNumber( $this->_getParam('page') );

    $this->view->paginator = $paginator;

    if (!$this->_getParam('content')){

      $this->_helper->content
      //->setNoRender()
          ->setEnabled();

    }


  }

  public function manageAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    $paginator = Engine_Api::_()->getDbTable('questions', 'hequestion')->getManageQuestionsPaginator($viewer);
    $paginator->setItemCountPerPage( Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hequestion.perpage', 10) );
    $paginator->setCurrentPageNumber( $this->_getParam('page') );

    $this->view->paginator = $paginator;


    if (!$this->_getParam('content')){

      $this->_helper->content
      //->setNoRender()
          ->setEnabled();

    }
  }

  public function viewAction()
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


    $this->_helper->content
    //->setNoRender()
        ->setEnabled();

  }

  public function boxAction()
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

    $this->_helper->content
    //->setNoRender()
        ->setEnabled();
  }

  public function createAction()
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
        $values['auth_view'] = array('everyone');
      }
      if( empty($values['auth_comment']) ) {
        $values['auth_comment'] = array('everyone');
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
      $action = $actionTable->addActivity($viewer, $subject, $type, null, array(
        'question' => '<a href="'.$question->getHref().'">'.$question->getTitle().'</a>'
      ));




      if ($action){

        $path = Zend_Controller_Front::getInstance()->getControllerDirectory('wall');
        $path = dirname($path) . '/views/scripts';
        $this->view->addScriptPath($path);


        Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $question);
        $this->view->body = $this->view->wallActivity($action, array(
          'module' => $this->_script_module
        ));
        $this->view->last_id = $action->getIdentity();
        $this->view->last_date = $action->date;
      }


      $db->commit();

      $this->view->result = true;


    } catch (Exception $e){
      $db->rollBack();
      throw $e;
    }


  }

  public function deleteAction()
  {
    $this->view->form = $form = new Hequestion_Form_Delete();
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if( !$this->_helper->requireAuth()->setAuthParams($question, null, 'delete')->isValid()) return;

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $question->delete();

    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('Your question has been deleted.');

    return $this->_forward('success' ,'utility', 'core', array(
      'parentRedirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'hequestion_general', true),
      'messages' => Array($this->view->message)
    ));

  }

  public function editAction()
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


    $options = $this->getRequest()->getPost('options');
    $form_options = array();

    if (!empty($options)){
      foreach ($options as $key => $option){
        $optionForm = new Hequestion_Form_Option();
        if ($optionForm->isValid(array('title' => $option))){
          $optionValues = $optionForm->getValues();
          $form_options[$key] = $optionValues['title'];
        }
      }
    }

    $db = Engine_Db_Table::getDefaultAdapter();
    $db->beginTransaction();

    try {

      $optionTable = Engine_Api::_()->getDbTable('options', 'hequestion');

      foreach ($form_options as $key => $item)
      {
        $option = $optionTable->fetchRow(array('option_id = ?' => $key));
        if (!$option){
          continue ; // ooops
        }
        $option->setFromArray(array('title' => $item['title']));
        $option->save();
      }

      $db->commit();


    } catch (Exception $e){
      $db->rollBack();
      throw $e;
    }


  }

  public function privacyAction()
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
      $values['auth_view'] = array('everyone');
    }
    if( empty($values['auth_comment']) ) {
      $values['auth_comment'] = array('everyone');
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

  public function editOptionsAction()
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


    $this->view->result = true;
    $this->view->body = $this->view->render('application/modules/Hequestion/views/scripts/_editOptions.tpl');

  }


  public function deleteOptionAction()
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


    $this->view->result = true;
    $this->view->question = $question;
    $this->view->only_content = true;
    $this->view->show_all = $this->_getParam('show_all');
    $this->view->body = $this->view->render('application/modules/Hequestion/views/scripts/_question.tpl');
    $this->view->widget = $this->renderWidget('hequestion.asked');


  }



  public function voteAction()
  {

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
        $action = $actionTable->addActivity($viewer, $question, 'hequestion_answer', null, array());
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

    $this->view->result = true;
    $this->view->question = $question;
    $this->view->only_content = true;
    $this->view->show_all = $this->_getParam('show_all');
    $this->view->body = $this->view->render('application/modules/Hequestion/views/scripts/_question.tpl');
    $this->view->widget = $this->renderWidget('hequestion.asked');

  }


  public function unvoteAction()
  {
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

    $this->view->result = true;
    $this->view->question = $question;
    $this->view->only_content = true;
    $this->view->show_all = $this->_getParam('show_all');
    $this->view->body = $this->view->render('application/modules/Hequestion/views/scripts/_question.tpl');
    $this->view->widget = $this->renderWidget('hequestion.asked');

  }



  public function followAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if ($viewer->getIdentity() && $question){
      Engine_Api::_()->getDbTable('followers', 'hequestion')->follow($viewer, $question);
    }

  }

  public function unfollowAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $question = Engine_Api::_()->getItem('hequestion', $this->_getParam('question_id'));

    if ($viewer->getIdentity() && $question){
      Engine_Api::_()->getDbTable('followers', 'hequestion')->unfollow($viewer, $question);
    }

  }

  public function askFriendsAction()
  {
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


    $user_ids = $this->_getParam('user_ids');
    $users = Engine_Api::_()->getItemMulti('user', $user_ids);
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

  }



  public function addAnswerAction()
  {
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



    $this->view->result = true;
    $this->view->question = $question;
    $this->view->only_content = true;
    $this->view->show_all = $this->_getParam('show_all');
    $this->view->body = $this->view->render('application/modules/Hequestion/views/scripts/_question.tpl');
    $this->view->widget = $this->renderWidget('hequestion.asked');


  }


  public function removeLinkAction()
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

  public function renderWidget($name, $params = array())
  {
    $structure = array(
      'type' => 'widget',
      'name' => $name,
      'action' => ( !empty($params['action']) ? $params['action'] : 'index' ),
    );
    if( !empty($params) ) {
      $structure['request'] = new Zend_Controller_Request_Simple('index',
        'index', 'core', $params);
    }
    $element = new Engine_Content_Element_Container(array(
      'elements' => array($structure)
    ));

    foreach( $element->getElements() as $cel ) {
      $cel->clearDecorators();
    }

    return $element->render();
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