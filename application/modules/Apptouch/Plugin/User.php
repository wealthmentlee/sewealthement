<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 17.02.12
 * Time: 15:28
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Plugin_User extends Zend_Controller_Plugin_Abstract
{
  public function onUserCreateAfter($event)
  {
    if(Engine_Api::_()->apptouch()->isApptouchMode())
      if( Engine_Api::_()->apptouch()->isApp() ) {
        Engine_Api::_()->getDbTable('statistics', 'core')->increment('ios.user.creations');
      } else {
        Engine_Api::_()->getDbTable('statistics', 'core')->increment('apptouch.user.creations');
      }

  }
}
