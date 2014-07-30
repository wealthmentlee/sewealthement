<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallActivityCheckins.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_View_Helper_WallActivityCheckins extends Zend_View_Helper_Abstract
{
  public function wallActivityCheckins($actions = array())
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('checkin')) {
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

    $checkinsTbl = Engine_Api::_()->getDbTable('checks', 'checkin');
    $checkins = $checkinsTbl->getListByActionIds($action_ids);

    $checkin_list = array();
    foreach ($checkins as $checkin) {
      $checkin_list[$checkin->action_id] = $checkin;
    }

    return $checkin_list;
  }
}