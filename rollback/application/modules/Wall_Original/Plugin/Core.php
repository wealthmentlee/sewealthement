<?php

class Wall_Plugin_Core
{



  public function onItemDeleteBefore($event)
  {
    $item = $event->getPayload();


    Engine_Api::_()->getDbtable('tags', 'wall')->delete(array(
      'object_type = ?' => $item->getType(),
      'object_id = ?' => $item->getIdentity(),
    ));

    if ($item instanceof Activity_Model_Action){

      Engine_Api::_()->getDbtable('tags', 'wall')->delete(array(
        'action_id = ?' => $item->getIdentity(),
      ));
/*      Engine_Api::_()->getDbtable('mute', 'wall')->delete(array(
        'action_id = ?' => $item->getIdentity(),
      ));
      Engine_Api::_()->getDbtable('privacy', 'wall')->delete(array(
        'action_id = ?' => $item->getIdentity(),
      ));*/

    }

    if ($item instanceof User_Model_User){

      Engine_Api::_()->getDbtable('tags', 'wall')->delete(array(
        'user_id = ?' => $item->getIdentity(),
      ));
/*      Engine_Api::_()->getDbtable('mute', 'wall')->delete(array(
        'user_id = ?' => $item->getIdentity(),
      ));
      */
    }


  }


}