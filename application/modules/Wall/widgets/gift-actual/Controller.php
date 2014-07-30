<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Widget_GiftActualController extends Engine_Content_Widget_Abstract
{

  public function indexAction()
  {
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();
    if (!$viewer && !$viewer->getIdentity()){
      return $this->setNoRender();
    }

    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('hegift')){
      return $this->setNoRender();
    }

    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');

    $values = array(
      'sort' => 'actual',
      'page' => 1,
      'category_id' => 0,
      'ipp' => 20,
      'amount' => true,
      'photo' => true,
      'enabled' => true,
      'status' => 1
    );


    $this->view->items = $gifts = $table->getGifts($values);

    if (!$gifts->getTotalItemCount()){
      return $this->setNoRender();
    }


  }

}