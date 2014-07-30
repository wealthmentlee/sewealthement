<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Mobile_Widget_AutorecommendationsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('suggest')) {
      $this->setNoRender();
      return ;
    }
    
    $api = Engine_Api::_()->suggest();
    $viewer = Engine_Api::_()->user()->getViewer();
    $id = md5('user_all_recommendations_'.$viewer->getIdentity());

    if (!$viewer->getIdentity()) {
      return $this->setNoRender();
    }
    
    $itemTypes = array_keys($api->getItemTypes());
    $recs = array();
    $user_id = $viewer->getIdentity();
    $empty = true;
    foreach ($itemTypes as $type) {
      if ($type == 'album_photo') {
        continue ;
      }
      
      $data = $api->getRecommendations($user_id, $type);
      $userArr = count($data['user']) > 0 ? $data['user'] : array();
      $adminArr = count($data['admin']) > 0 ? $data['admin'] : array();
      $result = array();
      if (!empty($adminArr)) {
        $result = $userArr;
        foreach ($adminArr as $item) {
          $result[] = $item;
        }
      } elseif (!empty($userArr)) {
        $result = $adminArr;
        foreach ($userArr as $item) {
          $result[] = $item;
        }
      }
      if (count($result)) {
        $index = rand(0, (count($result) - 1));
        $recs[$type] = $result[$index];
        $empty = false;
      }
    }

    if ($empty) {
      return $this->setNoRender();
    }

    if (!count($recs)) {
      return $this->setNoRender();
    }
        
    $this->view->items = $recs;
  }
}