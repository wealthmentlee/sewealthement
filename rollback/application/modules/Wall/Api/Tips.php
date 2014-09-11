<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Tips.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Api_Tips extends Core_Api_Abstract
{
  
  public function user($subject)
  {
    $menu_keys = array('userProfileFriend', 'userProfileMessage');
    $menus = array();
    foreach ($menu_keys as $menu){

      if (!method_exists($this, $menu)){
        continue ;
      }
      $result = $this->$menu($subject);
      if (!$result){
        continue ;
      }
      $menus[] = $result;
    }
    return $menus;
  }


  public function page($subject)
  {
    $menu_keys = array('pageProfileLike');
    $menus = array();
    foreach ($menu_keys as $menu){

      if (!method_exists($this, $menu)){
        continue ;
      }
      $result = $this->$menu($subject);
      if (!$result){
        continue ;
      }
      $menus[] = $result;
    }
    return $menus;
  }

  public function event($subject)
  {
    $menu_keys = array('eventProfileMember');
    $menus = array();
    foreach ($menu_keys as $menu){

      if (!method_exists($this, $menu)){
        continue ;
      }
      $result = $this->$menu($subject);
      if (!$result){
        continue ;
      }
      $menus[] = $result;
    }
    return $menus;
  }

  public function group($subject)
  {
    $menu_keys = array('groupProfileMember');
    $menus = array();
    foreach ($menu_keys as $menu){

      if (!method_exists($this, $menu)){
        continue ;
      }
      $result = $this->$menu($subject);
      if (!$result){
        continue ;
      }
      $menus[] = $result;
    }
    return $menus;
  }



  public function userProfileFriend($subject)
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    // Not logged in
    if( !$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false) )
    {
      return false;
    }

    // Check if friendship is allowed in the network
    $eligible = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.eligible', 2);
    if( !$eligible ){
      return '';
    }

    // check admin level setting if you can befriend people in your network
    else if( $eligible == 1 ){

      $networkMembershipTable = Engine_Api::_()->getDbtable('membership', 'network');
      $networkMembershipName = $networkMembershipTable->info('name');

      $select = new Zend_Db_Select($networkMembershipTable->getAdapter());
      $select
        ->from($networkMembershipName, 'user_id')
        ->join($networkMembershipName, "`{$networkMembershipName}`.`resource_id`=`{$networkMembershipName}_2`.resource_id", null)
        ->where("`{$networkMembershipName}`.user_id = ?", $viewer->getIdentity())
        ->where("`{$networkMembershipName}_2`.user_id = ?", $subject->getIdentity())
        ;

      $data = $select->query()->fetch();

      if( empty($data) ) {
        return '';
      }
    }

    // One-way mode
    $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
    if( !$direction ) {
      $viewerRow = $viewer->membership()->getRow($subject);
      $subjectRow = $subject->membership()->getRow($viewer);
      $params = array();

      // Viewer?
      if( null === $subjectRow ) {
        // Follow
        $params[] = array(
          'label' => 'Follow',
          'icon' => 'application/modules/User/externals/images/friends/add.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'add',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else if( $subjectRow->resource_approved == 0 ) {
        // Cancel follow request
        $params[] = array(
          'label' => 'Cancel Follow Request',
          'icon' => 'application/modules/User/externals/images/friends/remove.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'cancel',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else {
        // Unfollow
        $params[] = array(
          'label' => 'Unfollow',
          'icon' => 'application/modules/User/externals/images/friends/remove.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'remove',
            'user_id' => $subject->getIdentity()
          ),
        );
      }
      // Subject?
      if( null === $viewerRow ) {
        // Do nothing
      } else if( $viewerRow->resource_approved == 0 ) {
        // Approve follow request
        $params[] = array(
          'label' => 'Approve Follow Request',
          'icon' => 'application/modules/User/externals/images/friends/add.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'confirm',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else {
        // Remove as follower?
        $params[] = array(
          'label' => 'Remove as Follower',
          'icon' => 'application/modules/User/externals/images/friends/remove.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'remove',
            'user_id' => $subject->getIdentity(),
            'rev' => true,
          ),
        );
      }
      if( count($params) == 1 ) {
        return $params[0];
      } else if( count($params) == 0 ) {
        return false;
      } else {
        return $params;
      }
    }

    // Two-way mode
    else {
      $row = $viewer->membership()->getRow($subject);
      if( null === $row ) {
        // Add
        return array(
          'label' => 'Add to My Friends',
          'icon' => 'application/modules/User/externals/images/friends/add.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'add',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else if( $row->user_approved == 0 ) {
        // Cancel request
        return array(
          'label' => 'Cancel Friend Request',
          'icon' => 'application/modules/User/externals/images/friends/remove.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'cancel',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else if( $row->resource_approved == 0 ) {
        // Approve request
        return array(
          'label' => 'Approve Friend Request',
          'icon' => 'application/modules/User/externals/images/friends/add.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'confirm',
            'user_id' => $subject->getIdentity()
          ),
        );
      } else {
        // Remove friend
        return array(
          'label' => 'Remove from Friends',
          'icon' => 'application/modules/User/externals/images/friends/remove.png',
          'class' => 'smoothbox',
          'route' => 'user_extended',
          'params' => array(
            'controller' => 'friends',
            'action' => 'remove',
            'user_id' => $subject->getIdentity()
          ),
        );
      }
    }
  }

  
  public function userProfileMessage($subject)
  {
    // Not logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer->getIdentity() || $viewer->getGuid(false) === $subject->getGuid(false) ) {
      return false;
    }

    // Get setting?
    $permission = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'create');
    if( Authorization_Api_Core::LEVEL_DISALLOW === $permission )
    {
      return false;
    }
    $messageAuth = Engine_Api::_()->authorization()->getPermission($viewer->level_id, 'messages', 'auth');
    if( $messageAuth == 'none' ) {
      return false;
    } else if( $messageAuth == 'friends' ) {
      // Get data
      $direction = (int) Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 1);
      if( !$direction ) {
        //one way
        $friendship_status = $viewer->membership()->getRow($subject);
      }
      else $friendship_status = $subject->membership()->getRow($viewer);

      if( !$friendship_status || $friendship_status->active == 0 ) {
        return false;
      }
    }

    return array(
      'label' => "Send Message",
      'icon' => 'application/modules/Messages/externals/images/send.png',
      'route' => 'messages_general',
      'params' => array(
        'action' => 'compose',
         'to' => $subject->getIdentity()
      ),
    );
  }


  public function groupProfileMember($subject)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $subject->getType() !== 'group' )
    {
      throw new Group_Model_Exception('Whoops, not a group!');
    }

    if( !$viewer->getIdentity() )
    {
      return false;
    }

    $row = $subject->membership()->getRow($viewer);

    // Not yet associated at all
    if( null === $row )
    {
      if( $subject->membership()->isResourceApprovalRequired() ) {
        return array(
          'label' => 'Request Membership',
          'icon' => 'application/modules/Group/externals/images/member/join.png',
          'class' => 'smoothbox',
          'route' => 'group_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'request',
            'group_id' => $subject->getIdentity(),
          ),
        );
      } else {
        return array(
          'label' => 'Join Group',
          'icon' => 'application/modules/Group/externals/images/member/join.png',
          'class' => 'smoothbox',
          'route' => 'group_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'join',
            'group_id' => $subject->getIdentity()
          ),
        );
      }
    }

    // Full member
    // @todo consider owner
    else if( $row->active )
    {
      if( !$subject->isOwner($viewer) ) {
        return array(
          'label' => 'Leave Group',
          'icon' => 'application/modules/Group/externals/images/member/leave.png',
          'class' => 'smoothbox',
          'route' => 'group_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'leave',
            'group_id' => $subject->getIdentity()
          ),
        );
      } else {
        return array(
          'label' => 'Delete Group',
          'icon' => 'application/modules/Group/externals/images/delete.png',
          'class' => 'smoothbox',
          'route' => 'group_specific',
          'params' => array(
            'action' => 'delete',
            'group_id' => $subject->getIdentity()
          ),
        );
      }
    }

    else if( !$row->resource_approved && $row->user_approved )
    {
      return array(
        'label' => 'Cancel Membership Request',
        'icon' => 'application/modules/Group/externals/images/member/cancel.png',
        'class' => 'smoothbox',
        'route' => 'group_extended',
        'params' => array(
          'controller' => 'member',
          'action' => 'cancel',
          'group_id' => $subject->getIdentity()
        ),
      );
    }

    else if( !$row->user_approved && $row->resource_approved )
    {
      return array(
        array(
          'label' => 'Accept Membership Request',
          'icon' => 'application/modules/Group/externals/images/member/accept.png',
          'class' => 'smoothbox',
          'route' => 'group_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'accept',
            'group_id' => $subject->getIdentity()
          ),
        ), array(
          'label' => 'Ignore Membership Request',
          'icon' => 'application/modules/Group/externals/images/member/reject.png',
          'class' => 'smoothbox',
          'route' => 'group_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'reject',
            'group_id' => $subject->getIdentity()
          ),
        )
      );
    }

    else
    {
      throw new Group_Model_Exception('Wow, something really strange happened.');
    }


    return false;
  }

  
  public function eventProfileMember($subject)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if( $subject->getType() !== 'event' )
    {
      throw new Event_Model_Exception('Whoops, not a event!');
    }

    if( !$viewer->getIdentity() )
    {
      return false;
    }

    $row = $subject->membership()->getRow($viewer);

    // Not yet associated at all
    if( null === $row )
    {
      if( $subject->membership()->isResourceApprovalRequired() ) {
        return array(
          'label' => 'Request Invite',
          'icon' => 'application/modules/Event/externals/images/member/join.png',
          'class' => 'smoothbox',
          'route' => 'event_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'request',
            'event_id' => $subject->getIdentity(),
          ),
        );
      } else {
        return array(
          'label' => 'Join Event',
          'icon' => 'application/modules/Event/externals/images/member/join.png',
          'class' => 'smoothbox',
          'route' => 'event_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'join',
            'event_id' => $subject->getIdentity()
          ),
        );
      }
    }

    // Full member
    // @todo consider owner
    else if( $row->active )
    {
      if( !$subject->isOwner($viewer) ) {
        return array(
          'label' => 'Leave Event',
          'icon' => 'application/modules/Event/externals/images/member/leave.png',
          'class' => 'smoothbox',
          'route' => 'event_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'leave',
            'event_id' => $subject->getIdentity()
          ),
        );
      } else {
        return false;
        /*return array(
          'label' => 'Delete Event',
          'icon' => 'application/modules/Event/externals/images/delete.png',
          'class' => 'smoothbox',
          'route' => 'event_specific',
          'params' => array(
            'action' => 'delete',
            'event_id' => $subject->getIdentity()
          ),
        );*/
      }
    }

    else if( !$row->resource_approved && $row->user_approved )
    {
      return array(
        'label' => 'Cancel Invite Request',
        'icon' => 'application/modules/Event/externals/images/member/cancel.png',
        'class' => 'smoothbox',
        'route' => 'event_extended',
        'params' => array(
          'controller' => 'member',
          'action' => 'cancel',
          'event_id' => $subject->getIdentity()
        ),
      );
    }

    else if( !$row->user_approved && $row->resource_approved )
    {
      return array(
        array(
          'label' => 'Accept Event Invite',
          'icon' => 'application/modules/Event/externals/images/member/accept.png',
          'class' => 'smoothbox',
          'route' => 'event_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'accept',
            'event_id' => $subject->getIdentity()
          ),
        ), array(
          'label' => 'Ignore Event Invite',
          'icon' => 'application/modules/Event/externals/images/member/reject.png',
          'class' => 'smoothbox',
          'route' => 'event_extended',
          'params' => array(
            'controller' => 'member',
            'action' => 'reject',
            'event_id' => $subject->getIdentity()
          ),
        )
      );
    }

    else
    {
      throw new Event_Model_Exception('An error has occurred.');
    }


    return false;
  }


}