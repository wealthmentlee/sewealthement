<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-02-16 10:08:38 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Question_IndexController extends Core_Controller_Action_Standard
{
    protected $_navigation;
    protected $_create_question;

  public function indexAction()
  {

    if( !$this->_helper->requireAuth()->setAuthParams('question', null, 'view')->isValid() ) return;
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->question_title = Zend_Registry::get('Zend_Translate')->_('Questions and Answers');
    $this->view->navigation = $this->getNavigation();
    $this->view->form = $form = new Mobile_Form_Search();
    $this->view->user_id = $user_id = (int)$this->_getParam('user_id');
    $this->view->can_create = $this->can_create_question();
    $this->view->can_delete_question = $this->can_delete_question('everyone');

    $this->view->categories = Engine_Api::_()->question()->getCategories();

     // Process form
    $form->isValid($this->_getAllParams());

    $form->setAction($this->view->url(array('module' => 'question'), 'default', true) . '?user_id=' . $user_id );

    $values = $form->getValues();

    if ($user_id){
      $this->view->userObj = Engine_Api::_()->user()->getUser($user_id);
      $values['user_id'] = $user_id;
    }

    $this->view->assign($values);

    $this->view->paginator = $paginator = Engine_Api::_()->question()->getQuestionPaginator($values);
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->question_page;
    $paginator->setCurrentPageNumber( $this->_getParam('page'));
    $paginator->setItemCountPerPage($items_per_page);
  }
  public function manageAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;
    if (!$can_create = $this->_helper->requireAuth()->setAuthParams('question', null, 'create')->checkRequire()) return;
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->question_title = Zend_Registry::get('Zend_Translate')->_('Questions and Answers');
    $this->view->navigation = $this->getNavigation();
    $this->view->form = $form = new Mobile_Form_Search();
    $this->view->can_create = $this->can_create_question();
    $this->view->can_delete = $this->can_delete_question('owner');
    $this->view->categories = $categories = Engine_Api::_()->question()->getCategories();
     // Process form
    $form->isValid($this->_getAllParams());
    $values = $form->getValues();

    $values['user_id'] = $viewer->getIdentity();

    $this->view->assign($values);

    $this->view->paginator = $paginator = Engine_Api::_()->question()->getQuestionPaginator($values);
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->question_page;
    $paginator->setCurrentPageNumber( $this->_getParam('page'));
    $paginator->setItemCountPerPage($items_per_page);
  }
  public function createAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    if( !$this->can_create_question()) $this->_helper->requireAuth->forward();

    $this->view->form = $form = new Question_Form_Create();

    if (isset($form->file)){
      $form->removeElement('file');
    }

    $this->view->question_title = Zend_Registry::get('Zend_Translate')->_('Questions and Answers');
    $this->view->navigation = $this->getNavigation();
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {

      $questionTable = Engine_Api::_()->getItemTable('question');
      $values = $form->getValues();
      $viewer = Engine_Api::_()->user()->getViewer();

      // Begin database transaction
      $db = $questionTable->getAdapter();
      $db->beginTransaction();

      try
      {
        // Create the main hello world
        $values['search'] = 1;
        $questionRow = $questionTable->createRow();
        $questionRow->setFromArray($values);
        $questionRow->user_id = $viewer->getIdentity();
        $questionRow->owner_type = $viewer->getType();
        $questionRow->save();
        // Auth
          $auth = Engine_Api::_()->authorization()->context;
          $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

          foreach( $roles as $role ) {
            $auth->setAllowed($questionRow, $role, 'view', true);
          }
        $action= Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $questionRow, 'question_new', null, array('is_mobile' => true));
          // make sure action exists before attaching the blog to the activity
        if($action!=null){
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $questionRow);
        }
        Engine_Api::_()->question()->setrating('question', $viewer->getIdentity());
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      
      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  public function editAction() {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $viewer = $this->_helper->api()->user()->getViewer();
    $question = Engine_Api::_()->getItem('question', $this->_getParam('question_id'));
    if( !Engine_Api::_()->core()->hasSubject('question') )
    {
      Engine_Api::_()->core()->setSubject($question);
    }

    if( !$this->_helper->requireSubject()->isValid() ) return;
    if( !$this->_helper->requireAuth()->setAuthParams($question, $viewer, 'edit')->isValid() ) return;
    $this->view->question_title = Zend_Registry::get('Zend_Translate')->_('Questions and Answers');
    $this->view->navigation = $this->getNavigation(true);
    $this->view->form = $form = new Question_Form_Create();
    $form->setDescription(Zend_Registry::get('Zend_Translate')->_('Edit your question'))
         ->setTitle('Edit Question')
         ->populate($question->toArray());
    $form->submit->setLabel('Save Question');
    $this->view->categories = Engine_Api::_()->question()->getCategories();
    if( $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()) ) {
        $values = $form->getValues();
        $db = Engine_Db_Table::getDefaultAdapter();
        $db->beginTransaction();
        try
        {
          $question->setFromArray($values);
          $question->save();
          $db->commit();
        }
        catch( Exception $e )
        {
          $db->rollBack();
          throw $e;
        }
        return $this->_redirect("question/index/manage");
    }
  }
   
  public function viewAction() {
      if( !$this->_helper->requireAuth()->setAuthParams('question', null, 'view')->isValid() ) return;
         
      //$this->_helper->requireAuth()->setAuthParams('question', null, 'answer')->checkRequire();
    $this->view->navigation = $this->getNavigation();
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->question_title = Zend_Registry::get('Zend_Translate')->_('Questions and Answers');
    $question = Engine_Api::_()->getItem('question', $this->_getParam('question_id'));
    if( !Engine_Api::_()->core()->hasSubject('question') and $question instanceof Core_Model_Item_Abstract)
    {
      Engine_Api::_()->core()->setSubject($question);
    }

    if( !$this->_helper->requireSubject()->isValid() ) return;
        
    $question->question_views++;
    $question->save();
    $is_best_answer = $question->best_answer_id;

    $this->view->question = $question;
    $this->view->categories = $categories = Engine_Api::_()->question()->getCategories();
    if ( $this->getSession()->answer_add ) {
              $this->view->message = Zend_Registry::get('Zend_Translate')->_('The answer successfully added.');
              $this->getSession()->__unset('answer_add');
    }
    $createanswer = new Question_Form_CreateAnswer();
    if( $this->getRequest()->isPost() && $createanswer->isValid($this->getRequest()->getPost()) && Engine_Api::_()->question()->can_answer($question) === 0) {
      $answerTable = Engine_Api::_()->getDbtable('answers', 'question');
      $values = $createanswer->getValues();


      // Begin database transaction
      $db = $answerTable->getAdapter();
      $db->beginTransaction();
           
      try
      {
        $answerRow = $answerTable->createRow();
        $answerRow->setFromArray($values);
        $answerRow->user_id = $viewer->getIdentity();
        $answerRow->question_id = $question->question_id;
        $answerRow->save();

        // Auth
          $auth = Engine_Api::_()->authorization()->context;
          $roles = array('owner', 'owner_member', 'owner_member_member', 'owner_network', 'everyone');

          foreach( $roles as $role ) {
            $auth->setAllowed($answerRow, $role, 'view', true);
          }

        Engine_Api::_()->authorization()->removeAdapter('question_allow');
        $action= Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $answerRow, 'answer_new', '',  array('owner' => $question->getOwner('user')->getGuid(), 'is_mobile' => true));
       
        if($action!=null){
            Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $question);
        }
        Engine_Api::_()->question()->setrating('answer', $viewer->getIdentity());
        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }
      $this->getSession()->answer_add = true;
      $this->_helper->redirector->gotoRoute(array('route' => 'question_view', 'question_id' => $question->question_id));

    }
    $this->view->createanswer = $createanswer;
    $this->view->can_answer = Engine_Api::_()->question()->can_answer($question);
    $this->view->can_choose_answer = Engine_Api::_()->question()->can_choose_answer($question);
    $answer_param = array('question_id' => $question->question_id);
    if (is_numeric($is_best_answer) && $is_best_answer > 0) {
        $answer_param['best_answer_id'] = $is_best_answer;
        $this->view->best_answer = Engine_Api::_()->getItem('answer', $is_best_answer);
    }
    $this->view->paginator = $paginator = Engine_Api::_()->question()->getAnswerPaginator($answer_param);
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->question_page;

    $paginator->setCurrentPageNumber( $this->_getParam('page'));
    $paginator->setItemCountPerPage($items_per_page);
    
  }

  public function chooseAction() {

      $best_id = $this->_getParam('best_id');
      $answer = Engine_Api::_()->question()
                               ->getAnswerSelect(array('answer_id' => $best_id))
                               ->query();
      if ($answer->rowCount() != 1) return $this->_forward('requiresubject', 'error', 'core') ;
      $answer = $answer->fetch();
      $question = Engine_Api::_()->getItem('question', $answer['question_id']);
      if ( !Engine_Api::_()->question()->can_choose_answer($question)) return $this->_redirectCustom($question->getHref());
      $question->best_answer_id = $answer['answer_id'];
      $question->status = 'closed';
      $question->save();
      $viewer = $this->_helper->api()->user()->getViewer();
      Engine_Api::_()->authorization()->removeAdapter('question_allow');
      $action= Engine_Api::_()->getDbtable('actions', 'activity')->addActivity($viewer, $question, 'choose_best', null, array('is_mobile' => true));
      if($action!=null){
         Engine_Api::_()->getDbtable('actions', 'activity')->attachActivity($action, $question);
      }
      Engine_Api::_()->question()->setrating('best_answer', $answer['user_id']);
      return $this->_redirectCustom($question->getHref());
  }

  public function deleteAction()
  {
    $this->view->delete_title = 'Delete Answer?';
    $this->view->delete_description = 'Are you sure want to delete this answer? It will not be recoverable after being deleted.';
    $id = $this->_getParam('id');
    $this->view->answer_id=$id;
    $this->view->return_url = urldecode($this->_getParam('return_url'));
    // Check post
    if( $this->getRequest()->isPost())
    {
      $answer = Engine_Api::_()->getItem('answer', $id);
      $question = Engine_Api::_()->getItem('question', $answer->question_id);
      if( !Engine_Api::_()->core()->hasSubject('question') and $question instanceof Core_Model_Item_Abstract) {
          Engine_Api::_()->core()->setSubject($question);
      }
      if (!Engine_Api::_()->question()->can_delete_answer($answer)) {

          return $this->_forward('success', 'core-utility', 'mobile', array(
            'messages' => array(Zend_Registry::get('Zend_Translate')->_('You do not have rights to delete this answer.')),
            'return_url'=>urldecode($this->_getParam('return_url')),
          ));

      }
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();
      
      try
      {
        
        $answer->delete();
        $db->commit();
        Engine_Api::_()->getApi('settings', 'core')->setSetting('need_qarating_update', 1);
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this->_redirectCustom($question->getHref());
    }

  }

  public function answersAction()
  {

    if( !$this->_helper->requireAuth()->setAuthParams('question', null, 'view')->isValid() ) return;
    $viewer = $this->_helper->api()->user()->getViewer();
    $this->view->question_title = Zend_Registry::get('Zend_Translate')->_('Questions and Answers');
    $this->view->navigation = $this->getNavigation();
    $values = array();
    if ($user_q_id = $this->_getParam('user_id', false)) {
        $values['user_id'] = $user_q_id;
        $this->view->owner = Engine_Api::_()->getItem('user', $user_q_id);
    }
    $this->view->assign($values);

    $this->view->paginator = $paginator = Engine_Api::_()->question()->getAnswerPaginator($values);
    $items_per_page = Engine_Api::_()->getApi('settings', 'core')->question_page;
    $paginator->setCurrentPageNumber( $this->_getParam('page'));
    $paginator->setItemCountPerPage($items_per_page);
  }

  public function cancelAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    if( !$this->can_create_question()) $this->_helper->requireAuth->forward();

    $this->view->delete_title = 'Cancel Question?';
     $this->view->button = 'Ok';
    $this->view->delete_description = 'Are you sure that you want to cancel this question? It will not be recoverable after being canceled.';
    $id = $this->_getParam('id');
    $this->view->question_id=$id;
    $this->view->return_url = urldecode($this->_getParam('return_url'));
    // Check post
    if( $this->getRequest()->isPost())
    {

      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $question = Engine_Api::_()->getItem('question', $id);
        if ($question->status != 'open' or $question->user_id != $this->_helper->api()->user()->getViewer()->getIdentity())
            throw new Engine_Exception('You can\'t to cancel this question.');
        $question->status = 'canceled';
        $question->save();

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this->_redirectCustom($question->getHref());
    }

  }

  public function deleteqAction()
  {
    $this->view->delete_title = 'Delete Question?';
    $this->view->delete_description = 'Are you sure that you want to delete this question? It will not be recoverable after being deleted.';
    $id = $this->_getParam('id');
    $this->view->question_id=$id;
    $this->view->return_url = urldecode($this->_getParam('return_url'));
    // Check post
    if( $this->getRequest()->isPost())
    {
      $db = Engine_Db_Table::getDefaultAdapter();
      $db->beginTransaction();

      try
      {
        $question = Engine_Api::_()->getItem('question', $id);
        $tmp = $this->_helper->requireAuth()->setNoForward()->setAuthParams($question, null, 'del_question')->isValid();
        if (!$this->can_delete_question($question))
            throw new Engine_Exception('You can\'t to delete this question.');
        $question->delete();

        $db->commit();
      }

      catch( Exception $e )
      {
        $db->rollBack();
        throw $e;
      }

      return $this->_redirect($this->view->url(array('module' => 'question', 'action' => 'manage'), 'default', true));
    }

  }

  public function getNavigation($active = false)
  {
    if( is_null($this->_navigation) )
    {
      $navigation = $this->_navigation = new Zend_Navigation();

      $navigation->addPage(array(
        'label' =>  Zend_Registry::get('Zend_Translate')->_('Browse All'),
        'route' => 'default',
        'module' => 'question',
        'controller' => 'index',
        'action' => 'index'
      ));

      if( $this->_helper->api()->user()->getViewer()->getIdentity() )
      {

      if( $can_create = ($this->_helper->requireAuth()->setAuthParams('question', null, 'create')->checkRequire())){
        $navigation->addPage(array(
          'label' => Zend_Registry::get('Zend_Translate')->_('My Questions'),
          'route' => 'default',
          'module' => 'question',
          'controller' => 'index',
          'action' => 'manage',
          'active' => $active
        ));
       
        }
        if( $can_create ) {
            $navigation->addPage(array(
                'label' => Zend_Registry::get('Zend_Translate')->_('Ask a Question'),
                'route' => 'default',
                'module' => 'question',
                'controller' => 'index',
                'action' => 'create'
              ));
        }
      }
    }
    return $this->_navigation;
  }

  public function can_create_question() {
      if ($this->_create_question === null) {
          
          if (!$this->_helper->requireAuth()->setAuthParams('question', null, 'create')->checkRequire())
                  return $this->_create_question = false;
          if (!Engine_Api::_()->question()->is_valid_rating_setting('question_min_points_ask'))
                  return $this->_create_question = false;
          $this->_create_question = true;
      }
      return $this->_create_question;
  }

  public function can_delete_question($input){
      if ($this->_helper->requireAuth()->setAuthParams('question', null, 'del_question')->checkRequire()) {
            $user_Viewer = $this->_helper->api()->user()->getViewer();
            $allowed_view = unserialize(Engine_Api::_()->authorization()->getPermission($user_Viewer->level_id, 'question', 'del_question'));
            if (!$allowed_view) return false;
            if ($allowed_view == 'everyone') return true;
            if (is_string($input) and $allowed_view == $input) {
                return true;
            }
            elseif ($input instanceof Question_Model_Question){
                $user_role = ($input->isOwner($user_Viewer)) ? 'owner' : 'everyone';
                if ($user_role == $allowed_view) return true;
                else return false;
            }
            else return false;
      }
      else return false;
    
  }
}
