<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Plugin_Menus
{ 
	public function onMenuInitialize_UserEditInterests($row)
  {
		if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('like')){
  		return false;
  	}

		$viewer = Engine_Api::_()->user()->getViewer();
		$subject = Engine_Api::_()->core()->getSubject();

		if (!$subject->isSelf($viewer)){
			return false;
	  }

    return $row;
  }

  public  function onMenuInitialize_StoreProductProfilePromote($row)
  {
    /*if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('like')){
      return false;
    }

    $viewer = Engine_Api::_()->user()->getViewer();
    $subject = Engine_Api::_()->core()->getSubject();

    if($subject->isOwner($viewer)){
      $view = Zend_Registry::get('Zend_View');

      $url = $view->url(array( 'action' => 'promote', 'object_id' => $subject->getIdentity(), 'object' => 'store_product'), 'like_club', true);
        return array(
          'label' => 'LIKE_PromoteProduct',
          'icon' => 'application/modules/Like/externals/images/icons/promote_product.png',
          'uri' => $url,
          'class' => 'smoothbox'
        );

    }*/

    return false;
  }
}