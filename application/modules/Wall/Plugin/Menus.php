<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Menus {


  public function onMenuInitialize_WallAdminMainFacebook($row)
  {
    return true;
  }

  public function onMenuInitialize_WallAdminMainTwitter($row)
  {
    return true;
  }

}