<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_IndexController extends Core_Controller_Action_Standard
{
  protected $_script_module;

  public function init()
  {
    $this->_script_module = ($this->_getParam('is_timeline', false))? 'timeline':'wall';
  }

  public function postAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Get subject if necessary
    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = null;
    $subject_guid = $this->_getParam('subject', null);
    if( $subject_guid )
    {
      $subject = Engine_Api::_()->getItemByGuid($subject_guid);
    }
    // Use viewer as subject if no subject
    if( null === $subject )
    {
      $subject = $viewer;
    }

    // Make form
    $form = $this->view->form = new Wall_Form_Post();

    // Check auth
    if( !$subject->authorization()->isAllowed($viewer, 'comment') ) {
      return $this->_helper->requireAuth()->forward();
    }

    // Check if post
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Not post');
      return;
    }

    // Check if form is valid
    $postData = $this->getRequest()->getPost();
    
    $body = @$postData['body'];
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    $body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
    //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');
    $postData['body'] = $body;
    if( !$form->isValid($postData) )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    // Check one more thing
    if( $form->body->getValue() === '' && $form->getValue('attachment_type') === '' )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      return;
    }

    /**
     * set up action variable
     *
     * @var $action Wall_Model_Action
     */

    $action = null;

    // Process
    $db = Engine_Api::_()->getDbtable('actions', 'wall')->getAdapter();
    $db->beginTransaction();

    try
    {
      // Try attachment gettingf stuff
      $attachment = null;
      $attachmentData = $this->getRequest()->getParam('attachment');
      if( !empty($attachmentData) && !empty($attachmentData['type']) ) {
        $type = $attachmentData['type'];
        $config = null;

        $composer = Engine_Api::_()->wall()->getManifestType('wall_composer');
        if (!empty($composer[$type])){
          $config = $composer[$type];
        }
        if( !empty($config['auth']) && !Engine_Api::_()->authorization()->isAllowed($config['auth'][0], null, $config['auth'][1]) ) {
          $config = null;
        }
        if( $config ) {
          $plugin = Engine_Api::_()->loadClass($config['plugin']);
          $method = 'onAttach'.ucfirst($type);
          if (method_exists($plugin, $method)){
            $attachment = $plugin->$method($attachmentData);
          }
        }
      }


      // Get body
      $body = $form->getValue('body');
      $body = preg_replace('/<br[^<>]*>/', "\n", $body).' ';

      // Is double encoded because of design mode
      //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = html_entity_decode($body, ENT_QUOTES, 'UTF-8');
      //$body = htmlentities($body, ENT_QUOTES, 'UTF-8');

      // Special case=>status
      if( !$attachment && $viewer->isSelf($subject) )
      {
        if( $body != '' )
        {
          $viewer->status = $body;
          $viewer->status_date = date('Y-m-d H:i:s');
          $viewer->save();

          $viewer->status()->setStatus($body);
        }

        $action = Engine_Api::_()->getDbtable('actions', 'wall')->addActivity($viewer, $subject, 'status', $body, null, $this->_getParam('privacy'));



      }

      // General post
      else
      {
        /*
        if( $attachment )
        {
          $type = 'post_' . $attachment->getType();
        }
        else
        {
          $type = 'post';
        }
         */
        $type = 'post';
        if( $viewer->isSelf($subject) )
        {
          $type = 'post_self';
        }

        // Add notification for <del>owner</del> user
        $subjectOwner = $subject->getOwner();
        //if( !$viewer->isSelf($subjectOwner) )
        if( !$viewer->isSelf($subject) && $subject instanceof User_Model_User )
        {
          $notificationType = 'post_'.$subject->getType();
          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($subjectOwner, $viewer, $subject, $notificationType, array(
            'url1' => $subject->getHref(),
          ));
        }

        if( !$viewer->isSelf($subject) )
        {
          //if( $subject instanceof User_Model_User )
        }

        // Add activity
        $action = $this->_helper->api()->getDbtable('actions', 'wall')->addActivity($viewer, $subject, $type, $body, null, $this->_getParam('privacy'));

        // Try to attach if necessary
        if( $action && $attachment )
        {

          $this->_helper->api()->getDbtable('actions', 'wall')->attachActivity($action, $attachment);
        }

      }


      if ($action){

        $composerData = $this->getRequest()->getParam('composer');
        if( !empty($composerData)) {

          foreach (Engine_Api::_()->wall()->getManifestType('wall_composer') as $config){

            if (empty($config['composer'])){
              continue ;
            }

            $plugin = Engine_Api::_()->loadClass($config['plugin']);
            $method = 'onComposer'.ucfirst($config['type']);

            if (method_exists($plugin, $method)){
              $plugin->$method($composerData, array('action' => $action));
            }

          }

        }

        $tableToken = Engine_Api::_()->getDbTable('tokens', 'wall') ;
        $stream_services = $this->_getParam('share');

        try {

          if (!empty($stream_services)){


            foreach ($stream_services as $provider => $enabled){

              if (!$enabled){
                continue ;
              }
              $tokenRow = $tableToken->getUserToken($viewer, $provider);
              if (!$tokenRow){
                continue ;
              }
              $service = Engine_Api::_()->wall()->getServiceClass($provider);
              if (!$service->check($tokenRow)){
                continue ;
              }

              // :)))
              if (!empty($composerData) && !empty($composerData['fbpage_id']) && $composerData['fbpage_id'] != 'undefined' && $provider == 'facebook'){

                $fbpage_id = $composerData['fbpage_id'];

                $fbpageTable = Engine_Api::_()->getDbTable('fbpages', 'wall');
                $select = $fbpageTable->select()
                  ->where('user_id = ?', $viewer->getIdentity())
                  ->where('fbpage_id = ?', $fbpage_id);

                $fbpage = $fbpageTable->fetchRow($select);

                if ($fbpage){

                  $tokenRow->oauth_token = $fbpage->access_token; // :)
                  $service->postAction($tokenRow, $action, $viewer);

                }

              } else {

                $service->postAction($tokenRow, $action, $viewer);


              }


            }
          }

          if ($action){
            Engine_Api::_()->getDbTable('userSettings', 'wall')->saveLastPrivacy($action, $this->_getParam('privacy'), $viewer);
          }


        } catch (Exception $e){}

      }
      
      // @modified by Gitesh Dang
      // saving category
      
      $action->cat = $postData['cat'];
      $action->save();
	  // search entry
	  $sTable = Engine_Api::_()->getDbTable('search','core');
	  $sRow = $sTable->createRow();
	  $sRow->type = $action->getType();
	  $sRow->id = $action->getIdentity();
	  $sRow->title = $action->body;
	  $sRow->save();
      //ends @modified Gitesh Dang

      $db->commit();
    } // end "try"
    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->message = $e->getMessage();
      return ;
    }



    // If we're here, we're done
    $this->view->status = true;
    $this->view->message =  Zend_Registry::get('Zend_Translate')->_('Success!');
    if ($action){
      $mod = $this->_script_module;
      if($this->getRequest()->getParam('pinfeed') == 1 ){
        $mod = 'pinfeed';
      }

      $this->view->body = $this->view->wallActivity($action, array(
        'comment_pagination' => $this->_getParam('comment_pagination'),
        'module' => $mod
      ));
      $this->view->last_id = $action->getIdentity();
      $this->view->last_date = $action->date;
    }

    // Check if action was created
    $post_fail = "";
    if(!$action){
      $post_fail = "?pf=1";
    }
    //redirect if from pinfeed
    //print_die($this->view->action_id);

    // Redirect if in normal context


    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {

      $return_url = $form->getValue('return_url', false);

      if( $return_url )
      {
        return $this->_helper->redirector->gotoUrl($return_url.$post_fail, array('prependBase' => false));
      }
    }
  }


  public function likeAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $checkin = $this->_getParam('checkin', false);
    $pinfeed = $this->_getParam('pinfeed', false);
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');
    $viewer = $this->_helper->api()->user()->getViewer();

    // Start transaction
    $db = $this->_helper->api()->getDbtable('likes', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      $action = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);

      // Action
      if( !$comment_id ) {

        // Check authorization
        if( !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') ) {
          throw new Engine_Exception('This user is not allowed to like this item');
        }

        $action->likes()->addLike($viewer);

        // Add notification for owner of activity (if user and not viewer)
        if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() ) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);

          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
            'label' => 'post'
          ));
        }

      }
      // Comment
      else {
        $comment = $action->comments()->getComment($comment_id);

        // Check authorization
        if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment') ) {
          throw new Engine_Exception('This user is not allowed to like this item');
        }

        $comment->likes()->addLike($viewer);

        // @todo make sure notifications work right
        if( $comment->poster_id != $viewer->getIdentity() ) {
          Engine_Api::_()->getDbtable('notifications', 'activity')
            ->addNotification($comment->getPoster(), $viewer, $comment, 'liked', array(
              'label' => 'comment'
            ));
        }

        // Add notification for owner of activity (if user and not viewer)
        if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() ) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);

        }
      }

      // Stats
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.likes');

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->message = $e->getMessage();
      return ;
    }

    // Success
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You now like this action.');

    // Redirect if not json context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);

    }
    else if ('json'===$this->_helper->contextSwitch->getCurrentContext())
    {
      if($pinfeed){
        $module = 'pinfeed';
      }else{
        $module = $this->_script_module;
      }
      $this->view->body = $this->view->wallActivity($action, array(
        'checkin' => $checkin,
        'noList' => true,
        'comment_pagination' => $this->_getParam('comment_pagination'),
        'module' => $module,
      ));
    }
  }

  public function unlikeAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $checkin = $this->_getParam('checkin', false);
    $pinfeed = $this->_getParam('pinfeed', false);
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');
    $viewer = $this->_helper->api()->user()->getViewer();

    // Start transaction
    $db = $this->_helper->api()->getDbtable('likes', 'activity')->getAdapter();
    $db->beginTransaction();

    try {
      $action = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);

      // Action
      if( !$comment_id ) {

        // Check authorization
        if( !Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') ) {
          throw new Engine_Exception('This user is not allowed to unlike this item');
        }

        $action->likes()->removeLike($viewer);
      }

      // Comment
      else {
        $comment = $action->comments()->getComment($comment_id);

        // Check authorization
        if( !$comment || !Engine_Api::_()->authorization()->isAllowed($comment, null, 'comment') ) {
          throw new Engine_Exception('This user is not allowed to like this item');
        }

        $comment->likes()->removeLike($viewer);
      }

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->message = $e->getMessage();
      return ;
    }

    // Success
    $this->view->status = true;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You no longer like this action.');

    // Redirect if not json context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    else if ('json'===$this->_helper->contextSwitch->getCurrentContext())
    {
      if($pinfeed){
        $module = 'pinfeed';
      }else{
        $module = $this->_script_module;
      }
      $this->view->body = $this->view->wallActivity($action, array(
        'checkin' => $checkin,
        'noList' => true,
        'comment_pagination' => $this->_getParam('comment_pagination'),
        'module' => $module,
      ));
    }
  }

  public function viewcommentAction()
  {
    // Collect params
    $action_id = $this->_getParam('action_id');
    $viewer    = $this->_helper->api()->user()->getViewer();

    $action    = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);
    $form      = $this->view->form = new Wall_Form_Comment();
    $form->setActionIdentity($action_id);


    // Redirect if not json context
    if (null===$this->_getParam('format', null))
    {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    else if ('json'===$this->_getParam('format', null))
    {
      $this->view->body = $this->view->wallActivity($action, array(
        'viewAllComments' => true,
        'noList' => $this->_getParam('nolist', false),
        'comment_pagination' => $this->_getParam('comment_pagination'),
        'module' => $this->_script_module,
      ));
    }
  }

  public function commentAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Make form
    $this->view->form = $form = new Wall_Form_Comment();

    // Not post
    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Not a post');
      return;
    }

    // Not valid
    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error =  Zend_Registry::get('Zend_Translate')->_('Invalid data');
      $this->view->html = $form->render();
      return;
    }

    // Start transaction
    $db = $this->_helper->api()->getDbtable('actions', 'wall')->getAdapter();
    $db->beginTransaction();

    try
    {
      $viewer = $this->_helper->api()->user()->getViewer();
      $checkin = $this->_getParam('checkin', false);
      $pinfeed = $this->_getParam('pinfeed', false);
      $action_id = $this->view->action_id = $this->_getParam('action_id', $this->_getParam('action', null));
      $action = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);
      $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);
      $body = $form->getValue('body');

      // Check authorization
      if (!Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'))
        throw new Engine_Exception('This user is not allowed to comment on this item.');

      // Add the comment
      $action->comments()->addComment($viewer, $body);

      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');

      // Add notification for owner of activity (if user and not viewer)
      if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() )
      {
        $notifyApi->addNotification($actionOwner, $viewer, $action, 'commented', array(
          'label' => 'post'
        ));
      }

      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      foreach( $action->comments()->getAllCommentsUsers() as $notifyUser )
      {
        if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
        {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'commented_commented', array(
            'label' => 'post'
          ));
        }
      }

      // Add a notification for all users that commented or like except the viewer and poster
      // @todo we should probably limit this
      foreach( $action->likes()->getAllLikesUsers() as $notifyUser )
      {
        if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
        {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'liked_commented', array(
            'label' => 'post'
          ));
        }
      }

	  // Add a notification for all users that followed post
      // @todo we should probably limit this
      $wealthApi = Engine_Api::_()->wealthment();
      foreach( $wealthApi->getAllFollowers($action->action_id) as $notifyUser )
      {
        if( $notifyUser->getIdentity() != $viewer->getIdentity() && $notifyUser->getIdentity() != $actionOwner->getIdentity() )
        {
          $notifyApi->addNotification($notifyUser, $viewer, $action, 'post_follow', array(
            'label' => 'post'
          ));
        }
      }
	  
      // Stats
      Engine_Api::_()->getDbtable('statistics', 'core')->increment('core.comments');

      $db->commit();
    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->message = $e->getMessage();
      return ;
    }

    // Assign message for json
    $this->view->status = true;
    $this->view->message = 'Comment posted';


    // Redirect if not json
    if( null === $this->_getParam('format', null) )
    {
      $this->_redirect($form->return_url->getValue(), array('prependBase' => false));
    }
    else if ('json'===$this->_getParam('format', null))
    {
      if($pinfeed){
        $module = 'pinfeed';
      }else{
        $module = $this->_script_module;
      }
      $this->view->body = $this->view->wallActivity($action, array(
        'checkin' => $checkin,
        'noList' => true,
        'comment_pagination' => $this->_getParam('comment_pagination'),
        'module' => $module ,
      ));
      $this->view->id = ($action) ? $action->getIdentity(): 0;
    }
  }

  public function shareAction()
  {
    if( !$this->_helper->requireUser()->isValid() ) return;

    $this->view->type = $type = $this->_getParam('type');
    $this->view->id = $id = $this->_getParam('id');

    $viewer = Engine_Api::_()->user()->getViewer();
    $this->view->attachment = $attachment = Engine_Api::_()->getItem($type, $id);
    $this->view->form = $form = new Wall_Form_Share();

    $this->view->services = array_keys(Engine_Api::_()->wall()->getManifestType('wall_service'));

    if(!$attachment){
      // tell smoothbox to close
      $this->view->status  = true;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');
      $this->view->smoothboxClose = true;
      return $this->render('deletedItem');
    }

    if( !$this->getRequest()->isPost() )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid request method");
      return;
    }

    if( !$form->isValid($this->getRequest()->getPost()) )
    {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Invalid data");
      return;
    }

    // Process

    $db = Engine_Api::_()->getDbtable('actions', 'wall')->getAdapter();
    $db->beginTransaction();

    try
    {
      // Get body
      $body = $form->getValue('body');
      // Set Params for Attachment
      $params = array(
        'type' => '<a href="'.$attachment->getHref().'">'.$attachment->getMediaType().'</a>',
      );

      // Add activity
      $api = Engine_Api::_()->getDbtable('actions', 'activity');
      //$action = $api->addActivity($viewer, $viewer, 'post_self', $body);
      $action = $api->addActivity($viewer, $attachment->getOwner(), 'share', $body, $params);
      if( $action ) {
        $api->attachActivity($action, $attachment);
      }
      $db->commit();

      // Notifications
      $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
      // Add notification for owner of activity (if user and not viewer)
      if( $action->subject_type == 'user' && $attachment->getOwner()->getIdentity() != $viewer->getIdentity() )
      {
        $notifyApi->addNotification($attachment->getOwner(), $viewer, $action, 'shared', array(
          'label' => $attachment->getMediaType(),
        ));
      }

      $tableToken = Engine_Api::_()->getDbTable('tokens', 'wall') ;
      $stream_services = $this->_getParam('share');

      foreach ($stream_services as $provider => $enabled){

        if (!$enabled){
          continue ;
        }
        $tokenRow = $tableToken->getUserToken($viewer, $provider);
        if (!$tokenRow){
          continue ;
        }
        $service = Engine_Api::_()->wall()->getServiceClass($provider);
        if (!$service->check($tokenRow)){
          continue ;
        }
        $service->postAction($tokenRow, $action, $viewer);

      }



    }

    catch( Exception $e )
    {
      $db->rollBack();
      $this->view->status = false;
      $this->view->message = $e->getMessage();
      return ;
    }

    // If we're here, we're done
    $this->view->status = true;
    $this->view->message =  Zend_Registry::get('Zend_Translate')->_('Success!');

    // Redirect if in normal context
    if( null === $this->_helper->contextSwitch->getCurrentContext() ) {
      $return_url = $form->getValue('return_url', false);
      if( !$return_url ) {
        $return_url = $this->view->url(array(), 'default', true);
      }
      return $this->_helper->redirector->gotoUrl($return_url, array('prependBase' => false));
    } else if( 'smoothbox' === $this->_helper->contextSwitch->getCurrentContext() ) {
      $this->_forward('success', 'utility', 'core', array(
        'smoothboxClose' => 10,
        'parentRefresh'=> 10,
        'messages' => array('')
      ));
    }
  }

  function deleteAction()
  {
    $moduleTable = Engine_Api::_()->getDbTable('modules', 'core');
    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $viewer->level_id, 'activity');

    if( !$this->_helper->requireUser()->isValid() ) return;

    // Identify if it's an action_id or comment_id being deleted
    $checkin = $this->_getParam('checkin', false);
    $pinfeed = $this->_getParam('pinfeed', false);
    $this->view->comment_id = $comment_id = $this->_getParam('comment_id', null);
    $this->view->action_id  = $action_id  = $this->_getParam('action_id', null);

    $this->view->result  = false;
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You cannot share this item because it has been removed.');

    $action = Engine_Api::_()->getDbtable('actions', 'wall')->getActionById($action_id);
    if (!$action){
      return ;
    }

    // Send to view script if not POST
    if (!$this->getRequest()->isPost())
      return;


    // Both the author and the person being written about get to delete the action_id
    if (!$comment_id && (
        $activity_moderate ||
        ('user' == $action->subject_type && $viewer->getIdentity() == $action->subject_id) || // owner of profile being commented on
        ('user' == $action->object_type  && $viewer->getIdentity() == $action->object_id)))   // commenter
    {
      // Delete action item and all comments/likes
      $db = Engine_Api::_()->getDbtable('actions', 'wall')->getAdapter();
      $db->beginTransaction();
      try {
        $action->deleteItem();
        $db->commit();
        if ($moduleTable->isModuleEnabled('hashtag')) {
          $new = Engine_Api::_()->getDbTable('maps', 'hashtag');
          $select_b = $new->select()->where('resource_id = ?', $action_id);
          $tag = $new->fetchRow($select_b);
          if($tag->map_id > 0){
            $tags_hash = Engine_Api::_()->getDbTable('tags', 'hashtag');
            $map = $tags_hash->fetchRow($tags_hash->select()->where('map_id = ?', $tag->map_id));
            $map->delete();
          }
          $new->delete(array('resource_id = ?' => $action_id));
        }
        $this->view->result  = true;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('This activity item has been removed.');

        return ;

      } catch (Exception $e) {
        $db->rollback();
      }

    } elseif ($comment_id) {
      $comment = $action->comments()->getComment($comment_id);
      // allow delete if profile/entry owner
      $db = Engine_Api::_()->getDbtable('comments', 'activity')->getAdapter();
      $db->beginTransaction();
      if ($activity_moderate ||
        ('user' == $comment->poster_type && $viewer->getIdentity() == $comment->poster_id) ||
        ('user' == $action->object_type  && $viewer->getIdentity() == $action->object_id))
      {
        try {
          $action->comments()->removeComment($comment_id);
          $db->commit();
          $this->view->result = true;
          if($pinfeed){
            $module = 'pinfeed';
          }else{
            $module = $this->_script_module;
          }
          $this->view->message = Zend_Registry::get('Zend_Translate')->_('Comment has been deleted ');
          $this->view->body = $this->view->wallActivity($action, array(
            'checkin' => $checkin,
            'noList' => true,
            'comment_pagination' => $this->_getParam('comment_pagination'),
            'module' => $module,
          ));
          return ;
        } catch (Exception $e) {
          $db->rollback();
        }
      } else {
        $this->view->message = Zend_Registry::get('Zend_Translate')->_('You do not have the privilege to delete this comment');
        return ;
      }
    }

  }

  public function getLikesAction()
  {
    $action_id = $this->_getParam('action_id');
    $comment_id = $this->_getParam('comment_id');


    if( !$action_id ||
      !$comment_id ||
      !($action = Engine_Api::_()->getItem('activity_action', $action_id)) ||
      !($comment = $action->comments()->getComment($comment_id)) ) {
      $this->view->status = false;
      $this->view->body = '-';
      return;
    }

    $likes = $comment->likes()->getAllLikesUsers();
    $this->view->body = $this->view->translate(array('%s likes this', '%s like this',
      count($likes)), strip_tags($this->view->fluentList($likes)));
    $this->view->status = true;
  }


  public function viewAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    $table = Engine_Api::_()->getDbTable('actions', 'wall');

    $this->view->action = $action = $table->getPageAction($viewer, (int) $this->_getParam('id'));
    $this->view->comment_pagination = true;
    $this->view->comment_page = (int) $this->_getParam('comment_page');
    $this->view->viewAllLikes     = $this->_getParam('viewAllLikes',    $this->_getParam('show_likes',    false));


    if ($action && $action->getObject()){
      Engine_Api::_()->core()->setSubject($action->getObject());
    }


    // Instance
    $unique = rand(11111, 99999);
    $this->view->feed_uid = 'wall_' . $unique;

    if ($this->_getParam('format') == 'json'){

      if ($action){

        $activity_moderate = null;
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()){
          $activity_moderate = Engine_Api::_()->getDbtable('permissions', 'authorization')
            ->getAllowed('user', $viewer->level_id, 'activity');
        }

        $form = new Wall_Form_Comment();
        $this->view->assign(array(
          'actions' => array($action),
          'itemAction' => true,
          'commentForm' => $form,
          'user_limit' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userlength'),
          'allow_delete' => Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete'),
          'activity_moderate' =>$activity_moderate,
          'viewAllLikes' => $this->view->viewAllLikes
        ));
      }

      $this->view->result = true;
      $this->view->html = $this->view->render('_comments.tpl');

      return ;

    }

    $this->_helper->content
      //->setNoRender()
      ->setEnabled()
    ;

  }

  public function serviceShareAction()
  {
    $provider = $this->_getParam('provider');
    $viewer = Engine_Api::_()->user()->getViewer();

    $setting_key = 'share_' . $provider . '_enabled';

    $setting = Engine_Api::_()->wall()->getUserSetting($viewer);

    if (isset($setting->{$setting_key})){
      $setting->setFromArray(array($setting_key => (int) $this->_getParam('status', 0)));
      $setting->save();
    }
  }


  public function servicesRequestAction()
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer->getIdentity()){
      return ;
    }

    foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service){
      if ($this->_getParam($service)){
        $this->view->$service = false;
        $class = Engine_Api::_()->wall()->getServiceClass($service);
        if (!$class){
          continue ;
        }
        $token = Engine_Api::_()->getDbTable('tokens', 'wall')->getUserToken($viewer, $service);
        if (!$token){
          continue ;
        }
        if (!$token->check()){
          continue ;
        }
        $data = array_merge(array('enabled' => true), $token->publicArray());

        if ($service == 'facebook'){
          $data['fb_pages'] = $class->getPages($token);
        }

        $this->view->$service = $data;
      }
    }

  }


  public function suggestAction()
  {

    $select = Engine_Api::_()->wall()->getTagSuggest(Engine_Api::_()->user()->getViewer(), array('search' => $this->_getParam('value')));
    $paginator = Zend_Paginator::factory($select);


    $data = array();

    $paginator->setItemCountPerPage(50);
    foreach (Engine_Api::_()->wall()->getItems($paginator->getCurrentItems()) as $item){
      $data[] = array(
        'type'  => $item->getType(),
        'id'    => $item->getIdentity(),
        'guid'  => $item->getGuid(),
        'label' => $item->getTitle(),
        'photo' => $this->view->itemPhoto($item, 'thumb.icon'),
        'url'   => $item->getHref(),
      );
    }

    if( $this->_getParam('sendNow', true) )
    {
      return $this->_helper->json($data);
    }
    else
    {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }

  }


  public function suggestPeopleAction()
  {
    $select = Engine_Api::_()->wall()->getSuggestPeople(Engine_Api::_()->user()->getViewer(), array('search' => $this->_getParam('value')));
    $paginator = Zend_Paginator::factory($select);


    $data = array();

    $paginator->setItemCountPerPage(50);
    foreach (Engine_Api::_()->wall()->getItems($paginator->getCurrentItems()) as $item){
      $data[] = array(
        'type'  => $item->getType(),
        'id'    => $item->getIdentity(),
        'guid'  => $item->getGuid(),
        'label' => $item->getTitle(),
        'photo' => $this->view->itemPhoto($item, 'thumb.icon'),
        'url'   => $item->getHref(),
      );
    }

    if( $this->_getParam('sendNow', true) )
    {
      return $this->_helper->json($data);
    }
    else
    {
      $this->_helper->viewRenderer->setNoRender(true);
      $data = Zend_Json::encode($data);
      $this->getResponse()->setBody($data);
    }

  }





  public function changePrivacyAction()
  {
    $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));

    if (!$action || !$action->canChangePrivacy(Engine_Api::_()->user()->getViewer())){
      return ;
    }

    $action->changePrivacy($this->_getParam('privacy'));

  }


  public function muteAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $checkin = $this->_getParam('checkin', false);

    $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));
    if (!$action){
      return ;
    }
    $table = Engine_Api::_()->getDbTable('mute', 'wall');
    $viewer = Engine_Api::_()->user()->getViewer();

    $select = $table->select()
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('action_id = ?', $action->getIdentity());

    $mute = $table->fetchRow($select);

    if (!$mute){

      $mute = $table->createRow();
      $mute->setFromArray(array(
        'user_id' => $viewer->getIdentity(),
        'action_id' => $action->getIdentity()
      ));
      $mute->save();

    }

  }

  public function unmuteAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $checkin = $this->_getParam('checkin', false);

    $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));
    if (!$action){
      return ;
    }
    $table = Engine_Api::_()->getDbTable('mute', 'wall');
    $viewer = Engine_Api::_()->user()->getViewer();

    $select = $table->select()
      ->where('user_id = ?', $viewer->getIdentity())
      ->where('action_id = ?', $action->getIdentity());

    $mute = $table->fetchRow($select);

    if ($mute){
      $mute->delete();
    }


    $this->view->status = true;

    // Redirect if not json context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    else if ('json'===$this->_helper->contextSwitch->getCurrentContext())
    {
      $this->view->body = $this->view->wallActivity($action, array(
        'checkin' => $checkin,
        'noList' => true,
        'comment_pagination' => $this->_getParam('comment_pagination'),
        'module' => $this->_script_module,
      ));
    }

  }


  public function removeTagAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $checkin = $this->_getParam('checkin', false);

    $action = Engine_Api::_()->getItem('activity_action', $this->_getParam('action_id'));
    if (!$action){
      return ;
    }
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$action->canRemoveTag($viewer)){
      return ;
    }

    $table = Engine_Api::_()->getDbTable('tags', 'wall');

    $select = $table->select()
      ->where('action_id = ?', $action->getIdentity())
      ->where('object_type = ?', $viewer->getType())
      ->where('object_id = ?', $viewer->getIdentity());

    foreach ($table->fetchAll($select) as $tag){
      $tag->delete();
    }

    $this->view->status = true;

    // Redirect if not json context
    if( null === $this->_helper->contextSwitch->getCurrentContext() )
    {
      $this->_helper->redirector->gotoRoute(array(), 'default', true);
    }
    else if ('json'===$this->_helper->contextSwitch->getCurrentContext())
    {
      $this->view->body = $this->view->wallActivity($action, array(
        'checkin' => $checkin,
        'noList' => true,
        'comment_pagination' => $this->_getParam('comment_pagination'),
        'module' => $this->_script_module,
      ));
    }
  }




}
