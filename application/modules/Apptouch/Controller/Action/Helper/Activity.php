<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 09.08.12
 * Time: 10:43
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Controller_Action_Helper_Activity
  extends Apptouch_Controller_Action_Helper_Abstract
{

  public $checkins;
  public $pageEnabled;
  public $eventEnabled;
  public $isWall;
  public $privacy_list;

  public function loop($actions = null, array $data = array())
  {

    if (null == $actions || (!is_array($actions) && !($actions instanceof Zend_Db_Table_Rowset_Abstract))) {
      return '';
    }

    $this->isWall = 0;
    if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('wall')) {
      $this->isWall = 1;
    }
    if ($this->isWall) {
      $this->checkins = $this->view->wallActivityCheckins($actions);
      $this->pageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
      $this->eventEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event');
      $this->privacy_list = Engine_Api::_()->getDbTable('privacy', 'wall')->getPrivacyList($actions);
    }

    $actionsFormat = array();
    $hashtags = $this->wallActivityHashtags($actions);
    foreach ($actions as $action) {

      $af = $this->parseAction($action, $data);
      $id = $action->getIdentity();
      if(isset($hashtags[$id])){
        $af['hashtags'] = $hashtags[$id];
      }
      if($af)
        $actionsFormat[] = $af;
    }

    return $actionsFormat;
  }


  public function activity(Activity_Model_Action $action = null, array $data = array())
  {
    if (null === $action) {
      return '';
    }


    $this->isWall = 0;
    if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('wall')) {
      $this->isWall = 1;
    }
    if ($this->isWall) {
      $this->checkins = $this->view->wallActivityCheckins(array($action));
      $this->pageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
      $this->eventEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event');
      $this->privacy_list = Engine_Api::_()->getDbTable('privacy', 'wall')->getPrivacyList(array($action));
    }

    return $this->parseAction($action, $data);
  }

  private function subject(Core_Model_Item_Abstract $subject = null, $params = array())
  {
    if (!$subject) {
      if (Engine_Api::_()->core()->hasSubject()) {
        $subject = Engine_Api::_()->core()->getSubject();
      }
    }

    if (!$subject) {
      return null;
    }

    $format = array(
      'id' => $subject->getIdentity(),
      'type' => $subject->getType(),
      'title' => $subject->getTitle(),
      'href' => $subject->getHref(),
      'photo' => array()
    );
//    $host_url = (_ENGINE_SSL ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
//
//    $icon = false;
//    $empty = array();
//    foreach ($format['photo'] as $key => $value) {
//
//      $Headers = @get_headers($host_url . $value);
//      if (!strpos($Headers[0], '200')) {
//        //        $format['photo'][$key] = 'OOPS';
//        $empty[] = $key;
//      } else {
//        $icon = $value;
//      }
//    }
//    foreach ($empty as $key) {
//      if ($icon) {
//        $format['photo'][$key] = $icon;
//      }
//    }
    if (!empty($params)) {
      if (!empty($params['show_desc'])) {
        $format['description'] = $subject->getDescription();
        unset($params['show_desc']);
      }
      if (!empty($params['short_desc'])) {
        $format['short_desc'] = Engine_String::substr($subject->getDescription(), 0, 255) . '...';
        unset($params['short_desc']);
      }
      $format = array_merge($params, $format);
    }

    return $format;
  }


  public function parseAction($action, $params = array())
  {
    if (is_integer($action))
      $action = Engine_Api::_()->getItem('activity_action', $action);

    if (!$action instanceof Activity_Model_Action)
      return false;
    elseif (!$this->isValidAction($action)) {
      return false;
    }

    /**
     * @var $action Activity_Model_Action
     *
     */

    $subject = $action->getSubject();
    $object = $action->getObject();

    $isWall = $this->isWall;


    $actionFormat = $this->subject($action);
    unset($actionFormat['type']); // we don't need it

    $subjectFormat = array(
      'title' => $subject->getTitle(),
      'href' => $subject->getHref()
    );


    /**
     * Specially for the wall
     * If action by a page then to replace subject thumbnail and username to the page name
     */
    if ($isWall) {
      if (Engine_Api::_()->wall()->isOwnerTeamMember($action->getObject(), $action->getSubject())) {
        $subjectFormat = array(
          'title' => $object->getTitle(),
          'href' => $object->getHref()
        );
      }
    }


    $objectFormat = array(
      'title' => $object->getTitle(),
      'href' => $object->getHref()
    );


    /**
     * Prepare of content of the action
     */

    $content = $action->getContent();
    // View More fix. It is not quite solution :)
    //    todo CLEAR {
    $content = str_replace(
      '$(this).getParent().getNext().style.display=\'\';$(this).getParent().style.display=\'none\';',
      '$(this).parent().next().show();$(this).parent().hide()',
      $content
    );
    $content = str_replace(
      '$(this).getParent().getPrevious().style.display=\'\';$(this).getParent().style.display=\'none\';',
      '$(this).parent().prev().show();$(this).parent().hide()',
      $content
    );
    //    todo } CLEAR


    // Prepare a action of Likes Plugin
    $count_str = "likeCount : ";
    $count_pos = strpos($content, $count_str);
//    $count_posend = strpos($content, "}));});");

    if ($count_pos) {
      $search_pos = $count_pos + strlen($count_str);
      $likeCount = (int)(substr($content, $search_pos));

      if ($likeCount) {
          $start = strpos($content,'<div class="desc">');
          $offset = strpos($content,'</div>',$start);
          $content = substr($content,0,$offset+6). $this->view->translate('like_%s people like it', $likeCount) . substr($content,$offset+6);
      } else {
        $content = str_replace('<div class="desc"></div>', '<div class="desc">' . $this->view->translate('like_No one like it.') . '</div>', $content);
      }
    }

    // Cut scripts
    $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);

    /**
     * Tagged Peoples by Wall
     */
    $people_tags = array();
    if ($isWall && $action instanceof Wall_Model_Action) {

      $people_tags = $action->getPeopleTags();
      if (!empty($people_tags)) {

        $object_data = array();
        foreach ($people_tags as $item) {
          $object_data[] = array(
            'type' => $item->object_type,
            'id' => $item->object_id
          );
        }

        $object_fetch = Engine_Api::_()->wall()->getItems($object_data);
        $object_count = count($object_fetch);

        $sy = ' &mdash; ';
        if (empty($action->body)) {
          $sy = ' ' . $this->view->translate('WALL_is') . ' ';
        }

        if ($object_count == 0) {

        } else if ($object_count == 1) {
          $content .= '<span class="wall_with_people">' . $sy . '' . $this->view->translate('WALL_with %1$s', '<a href="' . $object_fetch[0]->getHref() . '" class="wall_liketips" rev="' . $object_fetch[0]->getGuid() . '">' . $object_fetch[0]->getTitle() . '</a>') . '</span>';
        } else if ($object_count == 2) {
          $content .= '<span class="wall_with_people">' . $sy . '' . $this->view->translate('WALL_with %1$s and %2$s', '<a href="' . $object_fetch[0]->getHref() . '" class="wall_liketips" rev="' . $object_fetch[0]->getGuid() . '">' . $object_fetch[0]->getTitle() . '</a>', '<a href="' . $object_fetch[1]->getHref() . '" class="wall_liketips" rev="' . $object_fetch[1]->getGuid() . '">' . $object_fetch[1]->getTitle() . '</a>') . '</span>';
        } else if ($object_count > 2) {

          $object_title = array();
          foreach ($object_fetch as $item) {
            $object_title[] = $item->getTitle();
          }

          $content .= '<span class="wall_with_people">' . $sy . '' . $this->view->translate('WALL_with %1$s and %2$s', '<a href="' . $object_fetch[0]->getHref() . '" class="wall_liketips" rev="' . $object_fetch[0]->getGuid() . '">' . $object_fetch[0]->getTitle() . '</a>', '<a href="javascript:void(0);" class="wall_tips" title="' . $this->view->wallFluentList($object_title) . '">' . $this->view->translate('WALL_%1$s others', $object_count) . '</a>') . '</span>';
        }
      }

    }

    /**
     * Details
     */
    $actionFormat['title'] = $content;
    $actionFormat['href'] = $this->view->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'view', 'action_id' => $action->getIdentity()), 'default', true);
    $actionFormat['subject'] = $subjectFormat;
    $actionFormat['object'] = $objectFormat;
    $actionFormat['creation_date'] = $this->view->timestamp($action->getTimeValue());

    $actionFormat['photo'] = $this->_bridge->getNoPhoto($subject, 'thumb.profile');
    if ($subject->getPhotoUrl()) {
      $actionFormat['photo'] = $subject->getPhotoUrl('thumb.icon');
      if ($isWall) {
        if (Engine_Api::_()->wall()->isOwnerTeamMember($action->getObject(), $action->getSubject())) {
          $actionFormat['photo'] = $object->getPhotoUrl('thumb.icon');
        }
      }

    }

    $canComment = ($action->getTypeInfo()->commentable &&
      $this->view->viewer()->getIdentity() &&
      Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment'));


    $actionFormat['commentable'] = false;
    if ($action->getTypeInfo()->commentable) {
      $actionFormat['commentable'] = true;
    }
    $actionFormat['canComment'] = $canComment;

    $actionFormat['canLike'] = false;
    if ($canComment) {
      $actionFormat['canLike'] = true;
      $actionFormat['liked'] = $action->likes()->isLike($this->view->viewer());
    }

    $actionFormat['comment_count'] = $action->comments()->getCommentCount();
    $actionFormat['like_count'] = $action->likes()->getLikeCount();
    try {
      if ($action->getAttachments() && current($action->getAttachments())->item) {
        $richContent = current($action->getAttachments())->item->getRichContent();
      } else {
        $richContent = null;
      }
    } catch (Exception $e) {
      $richContent = null;
    }
    if (
      count($action->getAttachments()) == 1 &&
      null != $richContent
    ) {
      $attachment = current($action->getAttachments())->item;

      if ($attachment->getType() == 'hequestion') { // Fix
        // ...
      } else {
        $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');

        $actionFormat['richContent'] = $this->_bridge->subject($attachment, array('short_desc' => true));
        if (!empty($actionFormat['richContent'])) {
          $actionFormat['richContent']['isVideo'] = (strpos($attachment->getType(), 'video') !== false && $attachment->type < 3);
          if($actionFormat['richContent']['isVideo']){
            $actionFormat['richContent']['photo']['full'] = Engine_Api::_()->apptouch()->getVideoThumbs($attachment, 'profile');
            if($actionFormat['richContent']['photo']['full']){
              if ($attachment->type == 1) {
                $actionFormat['richContent']['iframeUrl'] = $prefix.'www.youtube.com/embed/' . $attachment->code;
              }
              if ($attachment->type == 2) {
                $actionFormat['richContent']['iframeUrl'] = $prefix.'player.vimeo.com/video/' . $attachment->code;
              }
            }
            $flashObject = $attachment->getRichContent(true);
            $navigator = Engine_Api::_()->getApi('navigator', 'apptouch');
            $platform = $navigator->getUserAgent();
            if (strpos(strtolower($platform), 'android') !== false) {
              if ($flashObject) {
                $actionFormat['richContent']['flashObject'] = $flashObject;
              }
            }
          }
        }

      }

    } else {
      $attachmentsFormat = $this->parseActionAttachments($action);
      if (!empty($attachmentsFormat))
        $actionFormat['attachments'] = $attachmentsFormat;
    }

    /**
     * All of the options
     */

    $router = Zend_Controller_Front::getInstance()->getRouter();


    $actionFormat['actionLikeUrl'] = $router->assemble(array('module' => 'activity', 'controller' => 'index', 'action' => 'like', 'action_id' => $action->getIdentity(), 'format' => 'json'), 'default', true);
    $actionFormat['actionUnlikeUrl'] = $router->assemble(array('module' => 'activity', 'controller' => 'index', 'action' => 'unlike', 'action_id' => $action->getIdentity(), 'format' => 'json'), 'default', true);


    $viewer = Engine_Api::_()->user()->getViewer();
    $activity_moderate = $viewer->getIdentity() && Engine_Api::_()->getDbtable('permissions', 'authorization')
      ->getAllowed('user', $viewer->level_id, 'activity');
    $allow_delete = Engine_Api::_()->getApi('settings', 'core')->getSetting('activity_userdelete');


    // Delete
    $actionFormat['canDelete'] = false;
    $actionFormat['deleteUrl'] = false;

    if ($this->view->viewer()->getIdentity() && (
      $activity_moderate || (
        $allow_delete && (
          ('user' == $action->subject_type && $this->view->viewer()->getIdentity() == $action->subject_id) ||
            ('user' == $action->object_type && $this->view->viewer()->getIdentity() == $action->object_id)
        )
      ))
    ) {
      $actionFormat['deleteUrl'] = $router->assemble(array('module' => 'activity', 'controller' => 'index', 'action' => 'delete', 'action_id' => $action->getIdentity()), 'default', true);
      $actionFormat['canDelete'] = true;
    }


    // Share
    $actionFormat['canShare'] = false;
    $actionFormat['shareUrl'] = '';

    if ($action->getTypeInfo()->shareable && $this->view->viewer()->getIdentity()) {
      if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment())) {
        $actionFormat['shareUrl'] = $router->assemble(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity()), 'default', true);
        $actionFormat['canShare'] = true;
      } elseif ($action->getTypeInfo()->shareable == 2) {
        $actionFormat['shareUrl'] = $router->assemble(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity()), 'default', true);
        $actionFormat['canShare'] = true;
      } elseif ($action->getTypeInfo()->shareable == 3) {
        $actionFormat['shareUrl'] = $router->assemble(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity()), 'default', true);
        $actionFormat['canShare'] = true;
      } elseif ($action->getTypeInfo()->shareable == 4) {
        $actionFormat['shareUrl'] = $router->assemble(array('module' => 'activity', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity()), 'default', true);
        $actionFormat['canShare'] = true;
      }
    }


    // Link
    $actionFormat['showUrl'] = '';
    $actionFormat['canShow'] = false;
    if ($isWall) {
      $actionFormat['showUrl'] = Engine_Api::_()->wall()->getHostUrl() . $action->getHref();
      $actionFormat['canShow'] = true;
    }

    // Report
    $actionFormat['reportUrl'] = '';
    $actionFormat['canReport'] = false;
    if ($isWall && $this->view->viewer()->getIdentity()) {
      $actionFormat['reportUrl'] = $this->view->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $action->getGuid()), 'default', true);
      $actionFormat['canReport'] = true;
    }

    // Mute
    $actionFormat['muteUrl'] = '';
    $actionFormat['unmuteUrl'] = '';
    $actionFormat['canMute'] = false;
    if ($isWall && $this->view->viewer()->getIdentity() && !$action->isOwner($this->view->viewer())) {
      $actionFormat['muteUrl'] = $this->view->url(array('module' => 'wall', 'controller' => 'index', 'action' => 'mute', 'action_id' => $action->getIdentity(), 'format' => 'json'), 'default', true);
      $actionFormat['unmuteUrl'] = $this->view->url(array('module' => 'wall', 'controller' => 'index', 'action' => 'unmute', 'action_id' => $action->getIdentity(), 'format' => 'json'), 'default', true);
      $actionFormat['canMute'] = true;
    }

    // Remove tags
    $actionFormat['removeTagUrl'] = '';
    $actionFormat['removeTagCan'] = false;
    if ($isWall && $action instanceof Wall_Model_Action && $action->canRemoveTag($this->view->viewer()) && !$action->isOwner($this->view->viewer())) {
      $actionFormat['removeTagUrl'] = $this->view->url(array('module' => 'wall', 'controller' => 'index', 'action' => 'remove-tag', 'action_id' => $action->getIdentity(), 'format' => 'json'), 'default', true);
      $actionFormat['removeTagCan'] = true;
    }


    /**
     * Checkin
     */
    $actionFormat['checkin'] = array(
      'prefix' => '',
      'title' => '',
      'href' => ''
    );

    if ($isWall && Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('checkin')) {

      if (!empty($this->checkins) && isset($this->checkins[$action->getIdentity()]) && ($checkin = $this->checkins[$action->getIdentity()])) {
        if ($this->pageEnabled && $checkin->object_type == 'page') {
          $page = Engine_Api::_()->getItem($checkin->object_type, $checkin->object_id);
          $actionFormat['checkin']['prefix'] = $this->view->translate('at ');
          $actionFormat['checkin']['title'] = $page->getTitle();
          $actionFormat['checkin']['href'] = $page->getHref();
        } elseif ($this->eventEnabled && $checkin->object_type == 'event') {
          $event = Engine_Api::_()->getItem($checkin->object_type, $checkin->object_id);
          $actionFormat['checkin']['prefix'] = $this->view->translate('at ');
          $actionFormat['checkin']['title'] = $event->getTitle();
          $actionFormat['checkin']['href'] = $event->getHref();
        } else {
          $actionFormat['checkin']['prefix'] = $this->view->translate($this->view->wallLocationTypes($checkin->types));
          $actionFormat['checkin']['title'] = $checkin->name;
          $actionFormat['checkin']['href'] = $this->view->url(array('module' => 'checkin', 'controller' => 'index', 'action' => 'view-map', 'place_id' => $checkin->place_id), 'default', true);
        }
      }

    }


    /**
     * Privacy
     */
    $actionFormat['canPrivacy'] = false;
    $actionFormat['privacy'] = '';
    $actionFormat['privacyUrl'] = '';

    if ($isWall) {

      if ($action instanceof Wall_Model_Action && $action->canChangePrivacy($this->view->viewer())) {

        $privacy_type = $action->object_type;
        $privacy = array();
        $privacy_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.privacy.disabled', ''));
        foreach (Engine_Api::_()->wall()->getPrivacy($privacy_type) as $item) {
          if (in_array($privacy_type . '_' . $item, $privacy_disabled)) {
            continue;
          }
          $privacy[] = $item;
        }

        $privacy_active = (empty($privacy[0])) ? null : $privacy[0];
        if (!empty($this->privacy_list[$action->action_id]) && in_array($this->privacy_list[$action->action_id], $privacy)) {
          $privacy_active = $this->privacy_list[$action->action_id];
        }

        if ($privacy_active && count($privacy) > 1) {

          $actionFormat['canPrivacy'] = true;
          $actionFormat['privacy'] = array();
          $actionFormat['privacyUrl'] = $this->view->url(array('module' => 'wall', 'controller' => 'index', 'action' => 'change-privacy', 'action_id' => $action->getIdentity(), 'format' => 'json'), 'default', true);

          foreach ($privacy as $item) {
            $actionFormat['privacy'][] = array(
              'type' => $item,
              'title' => $this->view->translate('WALL_PRIVACY_' . strtoupper($privacy_type) . '_' . strtoupper($item)),
              'active' => ($item == $privacy_active)
            );
          }

        }

      }

    }


    /**
     * Build HTML
     */

    $this->view->action = $action;
    $this->view->actionFormat = $actionFormat;


    /**
     * remove $actionFormat from the response
     */
    $actionFormat = null;
    if (Engine_Api::_()->apptouch()->isTabletMode()) {
      $actionFormat['html'] = $this->view->render('application/modules/Apptablet/views/scripts/_feedItem.tpl');
    } else {
      $actionFormat['html'] = $this->view->render('_feedItem.tpl');
    }

    $actionFormat['content_menu'] = array();

    return $actionFormat;
  }

  private function isValidAction(Activity_Model_Action $action)
  {
    $available = Engine_Api::_()->apptouch()->getAvailableModules();
    $objModule = explode('_', get_class($action->getObject()) . '', 2);
    $objModule = strtolower($objModule[0]);
    $subjModule = explode('_', get_class($action->getSubject()) . '', 2);
    $subjModule = strtolower($subjModule[0]);
    return in_array($objModule, $available) &&
      in_array($subjModule, $available);
  }

  protected function parseActionAttachments($action)
  {
    if (is_integer($action))
      $action = Engine_Api::_()->getItem('activity_action', $action);

    if (!$action instanceof Activity_Model_Action)
      return false;
    if ($action->getTypeInfo()->attachable && $action->attachment_count > 0) {
      $attachmentsFormat = array();
      if ($action->getAttachments()) // todo
        foreach ($action->getAttachments() as $attachment) {
          $meta = $attachment->meta;

          // Silence
          if ($meta->mode == 0)
            continue;
          /**
           * @var $item Core_Model_Item_Abstract
           * */
          $item = $attachment->item;
          $is_album = (strpos($item->getType(), 'photo') !== false);
          $attachmentFormat = array();
          $attachmentFormat['mode'] = $meta->mode;
          $attachmentFormat['href'] = $item->getHref($is_album ? array('comments' => 'write') : array());
          $attachmentFormat['is_album'] = $is_album;
          $attachmentFormat['title'] = $item->getTitle();
          $attachmentFormat['id'] = $item->getIdentity();
          $attachmentFormat['type'] = $item->getType();
          $attachmentFormat['description'] = $item->getDescription();

          // Thumb/text/title type actions
          if ($meta->mode == 1) {
            $attachmentFormat['href'] = $item->getHref();
            if ($item->getPhotoUrl())
              $attachmentFormat['photo'] = $this->formatAttachmentPhoto($item);

            // Thumb only type actions
          } elseif ($meta->mode == 2)
            $attachmentFormat['photo'] = $this->formatAttachmentPhoto($item); // Description only type actions
          elseif ($meta->mode == 3) {
            $attachmentFormat['description'] = $item->getDescription();
          }

          $attachmentsFormat[] = $attachmentFormat;
        }
      return $attachmentsFormat;
    }
  }

  protected function formatAttachmentPhoto(Core_Model_Item_Abstract $item)
  {
    return array(
      'profile' => $item->getPhotoUrl('thumb.profile'),
      'normal' => $item->getPhotoUrl('thumb.normal'),
      'full' => $item->getPhotoUrl()
    );
  }

  public function getActivity(Activity_Model_DbTable_Actions $actionsTable, User_Model_User $user, array $params = array())
  {
    if ($actionsTable instanceof Wall_Model_DbTable_Actions) {
      return $this->_getWallActivity($actionsTable, $user, $params);
    } else if ($actionsTable instanceof Activity_Model_DbTable_Actions) {
      return $this->_getActivity($actionsTable, $user, $params);
    }

  }

  public function getActivityAbout(Activity_Model_DbTable_Actions $actionsTable, Core_Model_Item_Abstract $about, User_Model_User $user,
                                   array $params = array())
  {
    if ($actionsTable instanceof Timeline_Model_DbTable_Actions) {
      return $this->_getTimelineActivityAbout($actionsTable, $about, $user, $params);
    } else if ($actionsTable instanceof Wall_Model_DbTable_Actions) {
      return $this->_getWallActivityAbout($actionsTable, $about, $user, $params);
    } else if ($actionsTable instanceof Activity_Model_DbTable_Actions) {
      return $this->_getActivityAbout($actionsTable, $about, $user, $params);
    }

  }

  protected function _getActivity(Activity_Model_DbTable_Actions $actionsTable, User_Model_User $user, array $params = array())
  {
    // Proc args
    extract($this->_getInfo($actionsTable, $params)); // action_id, limit, min_id, max_id

    // Prepare main query
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $db = $streamTable->getAdapter();
    $union = new Zend_Db_Select($db);

    // Prepare action types
    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $masterActionTypes = $tableTypes->fetchAll($tableTypes->select()
      ->where('enabled = 1')
      ->where('displayable & 4')
      ->where('module IN (?)', Engine_Api::_()->apptouch()->getAvailableModules()));
    $mainActionTypes = array();
    // Filter out types set as not displayable
    foreach ($masterActionTypes as $type) {
      if ($type->displayable & 4) {
        $mainActionTypes[] = $type->type;
      }
    }

    // Filter types based on user request
    if (isset($showTypes) && is_array($showTypes) && !empty($showTypes)) {
      $mainActionTypes = array_intersect($mainActionTypes, $showTypes);
    } else if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes)) {
      $mainActionTypes = array_diff($mainActionTypes, $hideTypes);
    }

    // Nothing to show
    if (empty($mainActionTypes)) {
      return null;
    } // Show everything
    else if (count($mainActionTypes) == count($masterActionTypes)) {
      $mainActionTypes = true;
    } // Build where clause
    else {
      $mainActionTypes = "'" . join("', '", $mainActionTypes) . "'";
    }

    // Prepare sub queries
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $user,
    ));
    $responses = (array)$event->getResponses();

    if (empty($responses)) {
      return null;
    }

    foreach ($responses as $response) {
      if (empty($response)) continue;

      $select = $streamTable->select()
        ->from($streamTable->info('name'), 'action_id')
        ->where('target_type = ?', $response['type']);

      if (empty($response['data'])) {
        // Simple
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        // Single
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        // Array
        $select->where('target_id IN(?)', (array)$response['data']);
      } else {
        // Unknown
        continue;
      }

      // Add action_id/max_id/min_id
      if (null !== $action_id) {
        $select->where('action_id = ?', $action_id);
      } else {
        if (null !== $min_id) {
          $select->where('action_id >= ?', $min_id);
        } else if (null !== $max_id) {
          $select->where('action_id <= ?', $max_id);
        }
      }

      if ($mainActionTypes !== true) {
        $select->where('type IN(' . $mainActionTypes . ')');
      }

      // Add order/limit
      $select
        ->order('action_id DESC')
        ->limit($limit);

      // Add to main query
      $union->union(array('(' . $select->__toString() . ')')); // (string) not work before PHP 5.2.0
    }

    // Finish main query
    $union
      ->order('action_id DESC')
      ->limit($limit);

    // Get actions
    $actions = $db->fetchAll($union);

    // No visible actions
    if (empty($actions)) {
      return null;
    }

    // Process ids
    $ids = array();
    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    // Finally get activity
    return $actionsTable->fetchAll(
      $actionsTable->select()
        ->where('action_id IN(' . join(',', $ids) . ')')
        ->order('action_id DESC')
        ->limit($limit)
    );
  }

  protected function _getWallActivity(Activity_Model_DbTable_Actions $actionsTable, User_Model_User $user, $params = array())
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // params
    $limit = (empty($params['limit'])) ? $settings->getSetting('activity.length', 20) : (int)$params['limit'];
    $max_id = (empty($params['max_id'])) ? null : (int)$params['max_id'];
    $min_id = (empty($params['min_id'])) ? null : (int)$params['min_id'];
    $hideIds = (empty($params['hideIds'])) ? null : $params['hideIds'];
    $showTypes = (empty($params['showTypes'])) ? null : $params['showTypes'];
    $hideTypes = (empty($params['hideTypes'])) ? null : $params['hideTypes'];
    $action_id = (empty($params['action_id'])) ? null : (int)$params['action_id'];


    $mods = Engine_Api::_()->apptouch()->getAvailableModules();

    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $select = $tableTypes->select()
      ->where('enabled = 1')
      ->where('displayable & 4')
      ->where('module IN (?)', Engine_Api::_()->apptouch()->getAvailableModules());

    $total_types = $tableTypes->fetchAll($select);

    $types = array();
    foreach ($total_types as $item) {
      $types[] = $item->type;
    }


    if (!empty($showTypes) && is_array($showTypes)) {
      $types = array_intersect($types, $showTypes);
    }
    if (!empty($hideTypes) && is_array($hideTypes)) {
      $types = array_diff($types, $hideTypes);
    }


    if (empty($types)) {
      return null;
    }

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $user,
    ));
    $responses = (array)$event->getResponses();

    if (empty($responses)) {
      return null;
    }
    $tableStream = Engine_Api::_()->getDbTable('stream', 'activity');
    if (isset($params['hashtag']) && $params['hashtag']==1) {
      //MapsTable
      $mapsTable = Engine_Api::_()->getDbTable('maps','hashtag');
      $mTName = $mapsTable->info('name');
      $where = 1;
      $where_id = '';
      //TagsTable
      $tagsTable = Engine_Api::_()->getDbTable('tags','hashtag');
      $tTName = $tagsTable->info('name');
      if($params['hashtag_type'] == 'page'){
        $where = 'a.object_type = ?';
        $where_id = $params['hashtag_type'];
      }
      if($params['id'] != -1){
        $object = 'a.object_id = ?';
        $object_id = $params['id'];
      }else{
        $object = '';
        $object_id = '';
      }
      $friend_ids = array(0);
      $data = $data = $user->membership()->getMembershipsOfIds();
      ;
      if (!empty($data)) {
        $friend_ids = array_merge($friend_ids, $data);
      }
      $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
      $tagWhere = '
        (m.hashtagger_type = "user" AND  m.hashtagger_id = ' . $user->getIdentity() . ')
        or
        (ISNULL(p.action_id) OR p.privacy = "everyone" OR p.privacy = "registered")
        OR ((p.privacy = "networks" OR p.privacy = "members") AND m.hashtagger_type = "user" AND m.hashtagger_id IN (' . implode(",", $friend_ids) . ') )
        OR ((p.privacy = "owner" OR p.privacy = "page") AND m.hashtagger_type = "user" AND m.hashtagger_id = ' . $user->getIdentity() . ')
      ';
      $select = $actionsTable->select()->group('m.map_id')
        ->from(array('a' => $actionsTable->info('name')))
        ->joinInner(array('m' => $mTName), 'a.action_id = m.resource_id', array())
        ->joinInner(array('t' => $tTName), 'm.map_id = t.map_id', array())
        ->joinLeft(array('p' => $privacyTable->info('name')), 'p.action_id = m.resource_id', array())
        ->where('t.hashtag = ?', $params['hashtag_name'])->where($where, $where_id)->where(new Zend_Db_Expr($tagWhere));
          if($params['update']>0){
            $select->where('a.action_id > ?',$params['update']);
          }
      if($params['id'] != -1 && $params['hashtag_type'] == 'page')
        $select->where($object , $object_id);

         $select->order('a.action_id DESC');
      //$select->where("body like '%#".$params['hashtag_name']."%' ")->order('action_id desc');


      if (!empty($hideIds) && is_array($hideIds)) {
        $select->where('m.resource_id NOT IN (?)', $hideIds);
      }
      $fetch_all = $actionsTable->fetchAll($select);
     if($fetch_all[0]['action_id'] ==$params['update']){
      return;
      }

      return $fetch_all;

    }

    $where = '0';

    foreach ($responses as $response) {

      $where .= ' OR (target_type = "' . $response['type'] . '" AND ';

      if (empty($response['data'])) {
        $where .= 'target_id = 0';
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $where .= 'target_id = ' . $response['data'];
      } else if (is_array($response['data'])) {
        $where .= 'target_id IN (' . implode(",", (array)$response['data']) . ')';
      } else {
        continue;
      }

      $where .= ')';

    }

    $actionTable = Engine_Api::_()->getDbTable('actions', 'wall');

    $select = $actionTable->select()
      ->setIntegrityCheck(false)
      ->from(array('s' => $tableStream->info('name')), array('s.action_id'))
      ->where(new Zend_Db_Expr($where));

    $select
      ->group('s.action_id')
      ->order('s.action_id DESC')
      ->limit($limit);

    $select
      ->where('s.type IN (?)', $types);


    if (null !== $min_id) {
      $select->where('s.action_id >= ?', $min_id);
    } else if (null !== $max_id) {
      $select->where('s.action_id <= ?', $max_id);
    } else if ($action_id) {
      $select->where('s.action_id = ?', $action_id);
    }

    if (!empty($hideIds) && is_array($hideIds)) {
      $select->where('s.action_id NOT IN (?)', $hideIds);
    }

    if (isset($params['items']) && is_array($params['items'])) {

      if (!empty($params['items'])) {

        $where = "(";

        $group_items = array();
        foreach ($params['items'] as $item) {
          if (empty($group_items[$item['type']])) {
            $group_items[$item['type']] = array();
          }
          $group_items[$item['type']][] = $item['id'];
        }

        foreach ($group_items as $key => $item) {
          $where .= "(s.subject_type = '" . $key . "' AND s.subject_id IN (" . implode(",", $item) . ")) OR (s.object_type = '" . $key . "' AND s.object_id IN (" . implode(",", $item) . ")) OR ";
        }

        $where = substr($where, 0, -4);
        $where .= ")";


        if (!empty($where)) {
          $select->where(new Zend_Db_Expr($where));
        }

      } else {

        $select->where('0');

      }

    }


    $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
    $tableTag = Engine_Api::_()->getDbTable('tags', 'wall');


    // friends
    $friend_ids = array(0);
    $data = $data = $user->membership()->getMembershipsOfIds();
    ;
    if (!empty($data)) {
      $friend_ids = array_merge($friend_ids, $data);
    }

    $tagWhere = '
      (t.object_type = "user" AND t.object_id = ' . $user->getIdentity() . ')
      AND
      (ISNULL(p.action_id) OR p.privacy = "everyone" OR p.privacy = "registered")
      OR ((p.privacy = "networks" OR p.privacy = "members") AND t.object_type = "user" AND t.object_id IN (' . implode(",", $friend_ids) . ') )
      OR ((p.privacy = "owner" OR p.privacy = "page") AND t.object_type = "user" AND t.object_id = ' . $user->getIdentity() . ')
    ';

    $selectTag = $tableTag->select()
      ->setIntegrityCheck(false)
      ->from(array('t' => $tableTag->info('name')), array('t.action_id'))
      ->joinLeft(array('p' => $privacyTable->info('name')), 'p.action_id = t.action_id', array())
      ->where(new Zend_Db_Expr($tagWhere));


    /*    $selectTag
 ->where('s.type IN (?)', $types);*/


    if (null !== $min_id) {
      $selectTag->where('t.action_id >= ?', $min_id);
    } else if (null !== $max_id) {
      $selectTag->where('t.action_id <= ?', $max_id);
    } else if ($action_id) {
      $selectTag->where('t.action_id = ?', $action_id);
    }

    if (!empty($hideIds) && is_array($hideIds)) {
      $selectTag->where('t.action_id NOT IN (?)', $hideIds);
    }
    $selectTag->group('t.action_id');


    $db = Engine_Db_Table::getDefaultAdapter();

    $union = $db->select();
    $union->union(array('(' . $select->__toString() . ')'));
    $union->union(array('(' . $selectTag->__toString() . ')'));

    $union
      ->order('action_id DESC')
      ->limit($limit);


    $actions = $db->fetchAll($union);

    if (empty($actions)) {
      return null;
    }

    $ids = array();
    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    return $actionsTable->fetchAll(
      $actionsTable->select()
        ->where('action_id IN(' . join(',', $ids) . ')')
        ->order('action_id DESC')
        ->limit($limit)
    );

  }

  protected function _getActivityAbout(Activity_Model_DbTable_Actions $actionsTable, Core_Model_Item_Abstract $about, User_Model_User $user,
                                       array $params = array())
  {
    // Proc args
    extract($this->_getInfo($actionsTable, $params)); // action_id, limit, min_id, max_id

    // Prepare main query
    $streamTable = Engine_Api::_()->getDbtable('stream', 'activity');
    $db = $streamTable->getAdapter();
    $union = new Zend_Db_Select($db);

    // Prepare action types
    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $masterActionTypes = $tableTypes->fetchAll($tableTypes->select()
      ->where('enabled = 1')
      ->where('displayable & 4')
      ->where('module IN (?)', Engine_Api::_()->apptouch()->getAvailableModules()));

    $subjectActionTypes = array();
    $objectActionTypes = array();

    // Filter types based on displayable
    foreach ($masterActionTypes as $type) {
      if ($type->displayable & 1) {
        $subjectActionTypes[] = $type->type;
      }
      if ($type->displayable & 2) {
        $objectActionTypes[] = $type->type;
      }
    }

    // Filter types based on user request
    if (isset($showTypes) && is_array($showTypes) && !empty($showTypes)) {
      $subjectActionTypes = array_intersect($subjectActionTypes, $showTypes);
      $objectActionTypes = array_intersect($objectActionTypes, $showTypes);
    } else if (isset($hideTypes) && is_array($hideTypes) && !empty($hideTypes)) {
      $subjectActionTypes = array_diff($subjectActionTypes, $hideTypes);
      $objectActionTypes = array_diff($objectActionTypes, $hideTypes);
    }

    // Nothing to show
    if (empty($subjectActionTypes) && empty($objectActionTypes)) {
      return null;
    }

    if (empty($subjectActionTypes)) {
      $subjectActionTypes = null;
    } else if (count($subjectActionTypes) == count($masterActionTypes)) {
      $subjectActionTypes = true;
    } else {
      $subjectActionTypes = "'" . join("', '", $subjectActionTypes) . "'";
    }

    if (empty($objectActionTypes)) {
      $objectActionTypes = null;
    } else if (count($objectActionTypes) == count($masterActionTypes)) {
      $objectActionTypes = true;
    } else {
      $objectActionTypes = "'" . join("', '", $objectActionTypes) . "'";
    }

    // Prepare sub queries
    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $user,
      'about' => $about,
    ));
    $responses = (array)$event->getResponses();

    if (empty($responses)) {
      return null;
    }

    foreach ($responses as $response) {
      if (empty($response)) continue;

      // Target info
      $select = $streamTable->select()
        ->from($streamTable->info('name'), 'action_id')
        ->where('target_type = ?', $response['type']);

      if (empty($response['data'])) {
        // Simple
        $select->where('target_id = ?', 0);
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        // Single
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $select->where('target_id = ?', $response['data']);
      } else if (is_array($response['data'])) {
        // Array
        $select->where('target_id IN(?)', (array)$response['data']);
      } else {
        // Unknown
        continue;
      }

      // Add action_id/max_id/min_id
      if (null !== $action_id) {
        $select->where('action_id = ?', $action_id);
      } else {
        if (null !== $min_id) {
          $select->where('action_id >= ?', $min_id);
        } else if (null !== $max_id) {
          $select->where('action_id <= ?', $max_id);
        }
      }

      // Add order/limit
      $select
        ->order('action_id DESC')
        ->limit($limit);


      // Add subject to main query
      $selectSubject = clone $select;
      if ($subjectActionTypes !== null) {
        if ($subjectActionTypes !== true) {
          $selectSubject->where('type IN(' . $subjectActionTypes . ')');
        }
        $selectSubject
          ->where('subject_type = ?', $about->getType())
          ->where('subject_id = ?', $about->getIdentity());
        $union->union(array('(' . $selectSubject->__toString() . ')')); // (string) not work before PHP 5.2.0
      }

      // Add object to main query
      $selectObject = clone $select;
      if ($objectActionTypes !== null) {
        if ($objectActionTypes !== true) {
          $selectObject->where('type IN(' . $objectActionTypes . ')');
        }
        $selectObject
          ->where('object_type = ?', $about->getType())
          ->where('object_id = ?', $about->getIdentity());
        $union->union(array('(' . $selectObject->__toString() . ')')); // (string) not work before PHP 5.2.0
      }
    }

    // Finish main query
    $union
      ->order('action_id DESC')
      ->limit($limit);

    // Get actions
    $actions = $db->fetchAll($union);

    // No visible actions
    if (empty($actions)) {
      return null;
    }

    // Process ids
    $ids = array();
    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    // Finally get activity
    return $actionsTable->fetchAll(
      $actionsTable->select()
        ->where('action_id IN(' . join(',', $ids) . ')')
        ->order('action_id DESC')
        ->limit($limit)
    );
  }

  protected function _getWallActivityAbout(Activity_Model_DbTable_Actions $actionsTable, Core_Model_Item_Abstract $about, User_Model_User $user,
                                           array $params = array())
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // params
    $limit = (empty($params['limit'])) ? $settings->getSetting('activity.length', 20) : (int)$params['limit'];
    $max_id = (empty($params['max_id'])) ? null : (int)$params['max_id'];
    $min_id = (empty($params['min_id'])) ? null : (int)$params['min_id'];
    $hideIds = (empty($params['hideIds'])) ? null : $params['hideIds'];
    $showTypes = (empty($params['showTypes'])) ? null : $params['showTypes'];
    $hideTypes = (empty($params['hideTypes'])) ? null : $params['hideTypes'];
    $action_id = (empty($params['action_id'])) ? null : (int)$params['action_id'];


    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $select = $tableTypes->select()
      ->where('enabled = 1')
      ->where('displayable & 1 OR displayable & 2')
      ->where('module IN (?)', Engine_Api::_()->apptouch()->getAvailableModules());


    $total_types = $tableTypes->fetchAll($select);


    $types = array();
    foreach ($total_types as $item) {
      $types[] = $item->type;
    }


    if (!empty($showTypes) && is_array($showTypes)) {
      $types = array_intersect($types, $showTypes);
    }
    if (!empty($hideTypes) && is_array($hideTypes)) {
      $types = array_diff($types, $hideTypes);
    }

    $subjectActionTypes = array(0);
    $objectActionTypes = array(0);

    foreach ($total_types as $type) {
      if ($type->displayable & 1) {
        $subjectActionTypes[] = $type->type;
      }
      if ($type->displayable & 2) {
        $objectActionTypes[] = $type->type;
      }
    }


    if (empty($types)) {
      return null;
    }

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $user,
      'about' => $about
    ));
    $responses = (array)$event->getResponses();

    if (empty($responses)) {
      return null;
    }
    $tableStream = Engine_Api::_()->getDbTable('stream', 'activity');

    $where = '0';
    foreach ($responses as $response) {

      $where .= ' OR (target_type = "' . $response['type'] . '" AND ';

      if (empty($response['data'])) {
        $where .= 'target_id = 0';
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $where .= 'target_id = ' . $response['data'];
      } else if (is_array($response['data'])) {
        $where .= 'target_id IN (' . implode(",", (array)$response['data']) . ')';
      } else {
        continue;
      }

      $where .= ')';

    }

    $actionTable = Engine_Api::_()->getDbTable('actions', 'wall');

    $select = $actionTable->select()
      ->setIntegrityCheck(false)
      ->from(array('s' => $tableStream->info('name')), array('s.action_id'))
      ->where(new Zend_Db_Expr($where));


    $select
      ->where('s.type IN (?)', $types);


    if (null !== $min_id) {
      $select->where('s.action_id >= ?', $min_id);
    } else if (null !== $max_id) {
      $select->where('s.action_id <= ?', $max_id);
    } else if ($action_id) {
      $select->where('s.action_id = ?', $action_id);
    }

    if (!empty($hideIds) && is_array($hideIds)) {
      $select->where('s.action_id NOT IN (?)', $hideIds);
    }

    $select->where(new Zend_Db_Expr("(s.subject_type = '" . $about->getType() . "' AND s.subject_id = " . $about->getIdentity() . " AND s.type IN ('" . implode("','", $subjectActionTypes) . "') ) OR (s.object_type = '" . $about->getType() . "' AND s.object_id = " . $about->getIdentity() . " AND s.type IN ('" . implode("','", $objectActionTypes) . "') )"));


    if ($about->getType() == 'user') {
      if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
        $data = Engine_Api::_()->getDbtable('membership', 'page')->getMembershipsOfIds($about);
        if (!empty($data)) {
          $select->where('!(s.object_type = "page" AND s.object_id IN (?))', $data);
        }
      }
    }

    $select->group('s.action_id');


    $db = Engine_Db_Table::getDefaultAdapter();

    $union = $db->select();

    $union->union(array('(' . $select->__toString() . ')'));


    if ($about->getType() == 'user') {


      $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
      $tableTag = Engine_Api::_()->getDbTable('tags', 'wall');


      // friends
      $friend_ids = array(0);
      $data = $data = $user->membership()->getMembershipsOfIds();
      ;
      if (!empty($data)) {
        $friend_ids = array_merge($friend_ids, $data);
      }

      $tagWhere = '
        (t.object_type = "user" AND t.object_id = ' . $about->getIdentity() . ')
        AND
        (ISNULL(p.action_id) OR p.privacy = "everyone" OR p.privacy = "registered")
        OR ((p.privacy = "networks" OR p.privacy = "members") AND t.object_type = "user" AND t.object_id IN (' . implode(",", $friend_ids) . ') )
        OR ((p.privacy = "owner" OR p.privacy = "page") AND t.object_type = "user" AND t.object_id = ' . $user->getIdentity() . ')
      ';

      $selectTag = $tableTag->select()
        ->setIntegrityCheck(false)
        ->from(array('t' => $tableTag->info('name')), array('t.action_id'))
        ->joinLeft(array('p' => $privacyTable->info('name')), 'p.action_id = t.action_id', array())
        ->where(new Zend_Db_Expr($tagWhere));

      /*      $selectTag
   ->where('s.type IN (?)', $types);*/


      if (null !== $min_id) {
        $selectTag->where('t.action_id >= ?', $min_id);
      } else if (null !== $max_id) {
        $selectTag->where('t.action_id <= ?', $max_id);
      }

      if (!empty($hideIds) && is_array($hideIds)) {
        $selectTag->where('t.action_id NOT IN (?)', $hideIds);
      }
      $selectTag->group('t.action_id');


      $union->union(array('(' . $selectTag->__toString() . ')'));


    }


    $union
      ->order('action_id DESC')
      ->limit($limit);


    $actions = $db->fetchAll($union);

    if (empty($actions)) {
      return null;
    }

    $ids = array();
    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    return $actionsTable->fetchAll(
      $actionsTable->select()
        ->where('action_id IN(' . join(',', $ids) . ')')
        ->order('action_id DESC')
        ->limit($limit)
    );


  }

  protected function _getTimelineActivityAbout(Activity_Model_DbTable_Actions $actionsTable, Core_Model_Item_Abstract $about, User_Model_User $user,
                                               array $params = array())
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');

    // params
    $limit = (empty($params['limit'])) ? $settings->getSetting('activity.length', 20) : (int)$params['limit'];
    $max_id = (empty($params['max_id'])) ? null : (int)$params['max_id'];
    $min_id = (empty($params['min_id'])) ? null : (int)$params['min_id'];
    $hideIds = (empty($params['hideIds'])) ? null : $params['hideIds'];
    $showTypes = (empty($params['showTypes'])) ? null : $params['showTypes'];
    $hideTypes = (empty($params['hideTypes'])) ? null : $params['hideTypes'];
    $min_date = (empty($params['min_date'])) ? null : $params['min_date'];
    $max_date = (empty($params['max_date'])) ? null : $params['max_date'];


    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $select = $tableTypes->select()
      ->where('enabled = 1')
      ->where('displayable & 1 OR displayable & 2')
      ->where('module IN (?)', Engine_Api::_()->apptouch()->getAvailableModules());


    $total_types = $tableTypes->fetchAll($select);


    $types = array();
    foreach ($total_types as $item) {
      $types[] = $item->type;
    }


    if (!empty($showTypes) && is_array($showTypes)) {
      $types = array_intersect($types, $showTypes);
    }
    if (!empty($hideTypes) && is_array($hideTypes)) {
      $types = array_diff($types, $hideTypes);
    }

    $subjectActionTypes = array(0);
    $objectActionTypes = array(0);

    foreach ($total_types as $type) {
      if ($type->displayable & 1) {
        $subjectActionTypes[] = $type->type;
      }
      if ($type->displayable & 2) {
        $objectActionTypes[] = $type->type;
      }
    }


    if (empty($types)) {
      return null;
    }

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $user,
      'about' => $about
    ));
    $responses = (array)$event->getResponses();

    if (empty($responses)) {
      return null;
    }
    $tableStream = Engine_Api::_()->getDbTable('stream', 'activity');

    $where = '0';
    foreach ($responses as $response) {

      $where .= ' OR (target_type = "' . $response['type'] . '" AND ';

      if (empty($response['data'])) {
        $where .= 'target_id = 0';
      } else if (is_scalar($response['data']) || count($response['data']) === 1) {
        if (is_array($response['data'])) {
          list($response['data']) = $response['data'];
        }
        $where .= 'target_id = ' . $response['data'];
      } else if (is_array($response['data'])) {
        $where .= 'target_id IN (' . implode(",", (array)$response['data']) . ')';
      } else {
        continue;
      }

      $where .= ')';

    }

    $actionTable = Engine_Api::_()->getDbTable('actions', 'wall');

    $select = $actionTable->select()
      ->setIntegrityCheck(false)
      ->from(array('s' => $tableStream->info('name')), array())
      ->join(array('a' => $actionTable->info('name')), 'a.action_id = s.action_id', new Zend_Db_Expr('a.*'))
      ->where(new Zend_Db_Expr($where));


    $select
      ->where('s.type IN (?)', $types);


    if (null !== $min_date) {
      $select->where('(a.date > ?) || (a.date = ? && a.action_id > ' . (int)$min_id . ') ', $min_date);
    }
    if (null !== $max_date) {
      $select->where('(a.date < ?) || (a.date = ? && a.action_id < ' . (int)$max_id . ') ', $max_date);
    }


    /*    if( null !== $min_id ) {
      $select->where('a.action_id >= ?', $min_id);
    } else if( null !== $max_id ) {
      $select->where('a.action_id <= ?', $max_id);
    }*/

    if (!empty($hideIds) && is_array($hideIds)) {
      $select->where('a.action_id NOT IN (?)', $hideIds);
    }

    $select->where(new Zend_Db_Expr("(a.subject_type = '" . $about->getType() . "' AND a.subject_id = " . $about->getIdentity() . " AND s.type IN ('" . implode("','", $subjectActionTypes) . "') ) OR (a.object_type = '" . $about->getType() . "' AND a.object_id = " . $about->getIdentity() . " AND s.type IN ('" . implode("','", $objectActionTypes) . "') )"));


    if ($about->getType() == 'user') {
      if (Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page')) {
        $data = Engine_Api::_()->getDbtable('membership', 'page')->getMembershipsOfIds($about);
        if (!empty($data)) {
          $select->where('!(a.object_type = "page" AND a.object_id IN (?))', $data);
        }
      }
    }

    $select->group('s.action_id');


    $db = Engine_Db_Table::getDefaultAdapter();

    $union = $db->select();

    $union->union(array('(' . $select->__toString() . ')'));


    if ($about->getType() == 'user') {


      $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
      $tableTag = Engine_Api::_()->getDbTable('tags', 'wall');


      // friends
      $friend_ids = array(0);
      $data = $data = $user->membership()->getMembershipsOfIds();
      ;
      if (!empty($data)) {
        $friend_ids = array_merge($friend_ids, $data);
      }

      $tagWhere = '
        (t.object_type = "user" AND t.object_id = ' . $about->getIdentity() . ')
        AND
        (ISNULL(p.action_id) OR p.privacy = "everyone" OR p.privacy = "registered")
        OR ((p.privacy = "networks" OR p.privacy = "members") AND t.object_type = "user" AND t.object_id IN (' . implode(",", $friend_ids) . ') )
        OR ((p.privacy = "owner" OR p.privacy = "page") AND t.object_type = "user" AND t.object_id = ' . $user->getIdentity() . ')
      ';

      $selectTag = $tableTag->select()
        ->setIntegrityCheck(false)
        ->from(array('t' => $tableTag->info('name')), array())
        ->join(array('a' => $actionTable->info('name')), 'a.action_id = t.action_id', array('a.*'))
        ->joinLeft(array('p' => $privacyTable->info('name')), 'p.action_id = a.action_id', array())
        ->where(new Zend_Db_Expr($tagWhere));

      $selectTag
        ->where('a.type IN (?)', $types);


      if (null !== $min_date) {
        $selectTag->where('(a.date > ?) || (a.date = ? && a.action_id > ' . (int)$min_id . ') ', $min_date);
      }
      if (null !== $max_date) {
        $selectTag->where('(a.date < ?) || (a.date = ? && a.action_id < ' . (int)$max_id . ') ', $max_date);
      }


      /*      if( null !== $min_id ) {
        $selectTag->where('a.action_id >= ?', $min_id);
      } else if( null !== $max_id ) {
        $selectTag->where('a.action_id <= ?', $max_id);
      }*/

      if (!empty($hideIds) && is_array($hideIds)) {
        $selectTag->where('a.action_id NOT IN (?)', $hideIds);
      }
      $selectTag->group('t.action_id');


      $union->union(array('(' . $selectTag->__toString() . ')'));


    }


    $union
      ->order('action_id DESC')
      ->limit($limit);


    $actions = $db->fetchAll($union);

    if (empty($actions)) {
      return null;
    }

    $ids = array();
    foreach ($actions as $data) {
      $ids[] = $data['action_id'];
    }
    $ids = array_unique($ids);

    return $actionsTable->fetchAll(
      $actionsTable->select()
        ->where('action_id IN(' . join(',', $ids) . ')')
        ->group('action_id')
        ->order('date DESC')
        ->order('action_id DESC')
        ->limit($limit)
    );


  }

  protected function _getInfo($actions, array $params)
  {
    if ($actions instanceof Activity_Model_DbTable_Actions) {
      return $this->_getInfoDefault($params);
    } else if ($actions instanceof Wall_Model_DbTable_Actions) {
      return $this->_getInfoWall($params);
    } else if ($actions instanceof Timeline_Model_DbTable_Actions) {
      return $this->_getInfoTimeline($params);
    }
  }

  protected function _getInfoDefault(array $params)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $args = array(
      'limit' => $settings->getSetting('activity.length', 20),
      'action_id' => null,
      'max_id' => null,
      'min_id' => null,
      'showTypes' => null,
      'hideTypes' => null,
    );

    $newParams = array();
    foreach ($args as $arg => $default) {
      if (!empty($params[$arg])) {
        $newParams[$arg] = $params[$arg];
      } else {
        $newParams[$arg] = $default;
      }
    }

    return $newParams;
  }

  protected function _getInfoWall(array $params)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $args = array(
      'limit' => $settings->getSetting('activity.length', 20),
      'action_id' => null,
      'max_id' => null,
      'min_id' => null,
      'showTypes' => null,
      'hideTypes' => null,
      'hideIds' => null,
    );

    $newParams = array();
    foreach ($args as $arg => $default) {
      if (!empty($params[$arg])) {
        $newParams[$arg] = $params[$arg];
      } else {
        $newParams[$arg] = $default;
      }
    }

    return $newParams;
  }

  protected function _getInfoTimeline(array $params)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $args = array(
      'limit' => $settings->getSetting('activity.length', 20),
      'action_id' => null,
      'max_id' => null,
      'min_id' => null,
      'max_date' => null,
      'min_date' => null,
      'showTypes' => null,
      'hideTypes' => null,
      'hideIds' => null,
    );

    $newParams = array();
    foreach ($args as $arg => $default) {
      if (!empty($params[$arg])) {
        $newParams[$arg] = $params[$arg];
      } else {
        $newParams[$arg] = $default;
      }
    }

    return $newParams;
  }

  public function wallActivityHashtags($actions = array())
  {

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('hashtag') || Engine_Api::_()->core()->hasSubject()) {
      return array();
    }

    $action_ids = array();
    foreach ($actions as $action) {
      try {
        if (!$action->getTypeInfo()->enabled) {
          continue;
        }

        if (!$action->getSubject() || !$action->getSubject()->getIdentity()) {
          continue;
        }

        if (!$action->getObject() || !$action->getObject()->getIdentity()) {
          continue;
        }

        $action_ids[] = $action->getIdentity();

      } catch (Exception $e) {

      }
    }

    if (count($action_ids) == 0) {
      return array();
    }
   $actions_id = implode(',', $action_ids);
  $tagTable = Engine_Api::_()->getDbTable('tags', 'hashtag');
    $tTName = $tagTable->info('name');
    $mapsTable = Engine_Api::_()->getDbTable('maps', 'hashtag');
    $mTName = $mapsTable->info('name');

    $select = $tagTable->select()
      ->setIntegrityCheck(false)
      ->from(array('t' => $tTName))
      ->joinLeft(array('m' => $mTName), 't.map_id = m.map_id', array('resource_id'))
      ->where('m.resource_id IN ( ' . $actions_id . ' )');
/**
 * @var $tag_db Engine_Db_Table_Rowset
 * */
    $tag_db = $tagTable->fetchAll($select);
    $new_arr = array();
    foreach($tag_db->toArray() as $tag){
      if(!isset($new_arr[$tag['resource_id']]))
        $new_arr[$tag['resource_id']] = array($tag['hashtag']);
      else
        $new_arr[$tag['resource_id']][] = $tag['hashtag'];
    }

    return $new_arr;
  }

}
