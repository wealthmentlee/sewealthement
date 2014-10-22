<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 28.08.12
 * Time: 15:17
 * To change this template use File | Settings | File Templates.
 */

class Apptouch_Plugin_PageeventMenus
{
  public function onMenuInitialize_PageeventQuickInvite($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();
    $view = Zend_Registry::get('Zend_View');

    $membership = $subject->membership();
    $member = $membership->getRow($viewer);

    if ((($subject->isOwner($viewer) || $subject->getPage()->isAdmin($viewer)) || ($subject->invite && $member && $member->active)) && $membership->isFriends($viewer)) {
      $params = $row->params;
      if (empty($params['params']) || !is_array($params['params'])) {
        $params['params'] = array();
      }
      $params['params']['page_id'] = $subject->getPage()->getIdentity();
      $params['params']['event_id'] = $subject->getIdentity();

      $front_router = Zend_Controller_Front::getInstance()->getRouter();
      $query = http_build_query(array(
        'm' => 'pageevent',
        'l' => 'getInviteMembers',
        'c' => $view->url(array('action' => 'invite', 'page_id' => $subject->getPage()->getIdentity(), 'event_id' => $subject->getIdentity()), 'page_event'),
        't' => Zend_Registry::get('Zend_Translate')->_('PAGEEVENT_INVITE_TITLE'),
        'params' => array(
          'id' => $subject->getIdentity(),
          'disabled_label' => Zend_Registry::get('Zend_Translate')->_('PAGEEVENT_INVITE_DISABLED')
        )
      ));

      $href = $front_router->assemble(array(
        'module' => 'hecore',
        'controller' => 'index',
        'action' => 'contacts',
      ), 'default', true);

      return array(
        'label' => 'PAGEEVENT_INVITE',
        'icon' => 'application/modules/Suggest/externals/images/suggest.png',
        'class' => 'suggest_link',
        'uri' => $href . '?' . $query
      );
      //return $params;
    }

    return false;
  }

  public function onMenuInitialize_PageeventQuickEdit($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($subject->isOwner($viewer) || $subject->getPage()->isAdmin($viewer)) {
      $params = $row->params;
      if (empty($params['params']) || !is_array($params['params'])) {
        $params['params'] = array();
      }
      $params['params']['page_id'] = $subject->getPage()->getIdentity();
      $params['params']['event_id'] = $subject->getIdentity();

      return $params;
    }

    return false;
  }

  public function onMenuInitialize_PageeventQuickDelete($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    if ($subject->isOwner($viewer) || $subject->getPage()->isAdmin($viewer)) {
      $params = $row->params;
      if (empty($params['params']) || !is_array($params['params'])) {
        $params['params'] = array();
      }
      $params['params']['page_id'] = $subject->getPage()->getIdentity();
      $params['params']['event_id'] = $subject->getIdentity();

      return $params;
    }

    return false;
  }

  public function onMenuInitialize_PageeventQuickLeave($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    $membership = $subject->membership();
    $member = $membership->getRow($viewer);

    if ($member && $member->active && !$subject->isOwner($viewer)) {
      $params = $row->params;
      if (empty($params['params']) || !is_array($params['params'])) {
        $params['params'] = array();
      }
      $params['params']['page_id'] = $subject->getPage()->getIdentity();
      $params['params']['event_id'] = $subject->getIdentity();
      $params['params']['approve'] = 0;

      return $params;
    }

    return false;
  }

  public function onMenuInitialize_PageeventQuickWaiting($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    $membership = $subject->membership();
    $count_waiting = $membership->getWaitingCount();
    if (($subject->isOwner($viewer) || $subject->getPage()->isAdmin($viewer)) && $count_waiting) {
      $params = $row->params;
      if (empty($params['params']) || !is_array($params['params'])) {
        $params['params'] = array();
      }
      $params['params']['page_id'] = $subject->getPage()->getIdentity();
      $params['params']['event_id'] = $subject->getIdentity();

      return $params;
    }

    return false;
  }

  public function onMenuInitialize_PageeventQuickCancel($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    $membership = $subject->membership();
    $member = $membership->getRow($viewer);

    if ($member && !$member->resource_approved) {
      $params = $row->params;

      if (empty($params['params']) || !is_array($params['params'])) {
        $params['params'] = array();
      }

      $params['params']['page_id'] = $subject->getPage()->getIdentity();
      $params['params']['event_id'] = $subject->getIdentity();
      $params['params']['approve'] = 0;

      return $params;
    }

    return false;
  }

  public function onMenuInitialize_PageeventQuickRequest($row)
  {
    $subject = Engine_Api::_()->core()->getSubject();
    $viewer = Engine_Api::_()->user()->getViewer();

    $membership = $subject->membership();
    $member = $membership->getRow($viewer);

    if (!$member && $viewer->getIdentity() && $subject->approval) {
      $params = $row->params;

      if (empty($params['params']) || !is_array($params['params'])) {
        $params['params'] = array();
      }

      $params['params']['page_id'] = $subject->getPage()->getIdentity();
      $params['params']['event_id'] = $subject->getIdentity();
      $params['params']['rsvp'] = 0;

      return $params;
    }

    return false;
  }

}