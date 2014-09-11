<?php


class Wall_View_Helper_WallTranslates extends Zend_View_Helper_Abstract
{

  public function wallTranslates($items, $translate = false)
  {

    return array(
      'WALL_CONFIRM_ACTION_REMOVE_TITLE',
      'WALL_CONFIRM_ACTION_REMOVE_DESCRIPTION',
      'WALL_CONFIRM_COMMENT_REMOVE_TITLE',
      'WALL_CONFIRM_COMMENT_REMOVE_DESCRIPTION',
      'WALL_CONFIRM_LIST_REMOVE_TITLE',
      'WALL_CONFIRM_LIST_REMOVE_DESCRIPTION',
      'WALL_LIKE',
      'WALL_UNLIKE',
      'Save',
      'Cancel',
      'delete',
      'WALL_LOADING',
      'WALL_STREAM_EMPTY_VIEWALL',
      'WALL_EMPTY_FEED',
      'WALL_CAMERA_FREEZE',
      'WALL_CAMERA_CANCEL',
      'WALL_CAMERA_UPLOAD',
      'WALL_COMPOSE_CAMERA',
      'WALL_TWITTER_RETWEETED',
      'WALL_CONFIRM_TWITTER_DELETE_TITLE',
      'WALL_CONFIRM_TWITTER_DELETE_DESCRIPTION',
      'WALL_SENDING',
      'WALL_Share',
      'WALL_Who are you with?',
      'WALL_with %1$s',
      'WALL_with %1$s and %2$s',
      'WALL_%1$s others',
      'WALL_Link to this post',
      'WALL_Copy this link to send a copy of this post to others:',
      'WALL_GO',
      'WALL_No longer seeing this post.',
      'WALL_Undo mute',
      'WALL_CONFIRM_REMOVE_TAG_TITLE',
      'WALL_CONFIRM_REMOVE_TAG_DESCRIPTION',

      'WALL_PRIVACY_USER_EVERYONE',
      'WALL_PRIVACY_USER_NETWORKS',
      'WALL_PRIVACY_USER_MEMBERS',
      'WALL_PRIVACY_USER_OWNER',
      'WALL_PRIVACY_PAGE_EVERYONE',
      'WALL_PRIVACY_PAGE_REGISTERED',
      'WALL_PRIVACY_PAGE_PAGE',

      'WALL_USER_NETWORKS_TAGGED',
      'WALL_USER_MEMBERS_TAGGED',
      'WALL_USER_OWNER_TAGGED',
      'WALL_PAGE_PAGE_TAGGED',

      'WALL_CHOOSE_MY_PAGE',
      'WALL_FBPAGE_NO',

      'WALL_SHARE_LINKEDIN'
    );

  }

}
