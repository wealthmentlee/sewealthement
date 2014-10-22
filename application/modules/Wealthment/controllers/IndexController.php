<?php

class Wealthment_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
  }
  
  public function followAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    $action_id = $this->_getParam('action_id');
    $pinfeed = $this->_getParam('pinfeed', false);
    $viewer = $this->_helper->api()->user()->getViewer();

    // Start transaction
    $table = $this->_helper->api()->getDbtable('follows', 'wealthment');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $action = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);
      $values['object_type'] = $action->getObject()->getType();
      $values['object_id'] = $action->getObject()->getIdentity();
      $values['follower_id'] = $viewer->getIdentity();
      $values['action_id'] = $action_id;
      
      $follow = $table->createRow();
      $follow->setFromArray($values);
      $follow->save();
      
        // Add notification for owner of activity (if user and not viewer)
        /*if( $action->subject_type == 'user' && $action->subject_id != $viewer->getIdentity() ) {
          $actionOwner = Engine_Api::_()->getItemByGuid($action->subject_type."_".$action->subject_id);

          Engine_Api::_()->getDbtable('notifications', 'activity')->addNotification($actionOwner, $viewer, $action, 'liked', array(
            'label' => 'post'
          ));
        }*/
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
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You are following this action');

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
        'noList' => true,
        'comment_pagination' => $this->_getParam('comment_pagination'),
        'module' => $module,
      ));
    }
  }

  public function unfollowAction()
  {
    // Make sure user exists
    if( !$this->_helper->requireUser()->isValid() ) return;

    // Collect params
    $pinfeed = $this->_getParam('pinfeed', false);
    $action_id = $this->_getParam('action_id');
    $viewer = $this->_helper->api()->user()->getViewer();

    // Start transaction
    $table = $this->_helper->api()->getDbtable('follows', 'wealthment');
    $db = $table->getAdapter();
    $db->beginTransaction();

    try {
      $action = $this->_helper->api()->getDbtable('actions', 'wall')->getActionById($action_id);
      $api = Engine_Api::_()->wealthment();
      $follow = $api->getFollowed($action_id,$viewer);
      
      $follow->delete();
      
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
    $this->view->message = Zend_Registry::get('Zend_Translate')->_('You no longer follow this action.');

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
        'noList' => true,
        'comment_pagination' => $this->_getParam('comment_pagination'),
        'module' => $module,
      ));
    }
  }
  
}
