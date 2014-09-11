<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallElemDesc.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Form_Decorator_WallElemDesc extends Zend_Form_Decorator_Abstract
{

  public function render($content)
  {
    $description = $this->getOption('description');
    if ($description){
      return '<div class="wall-elem-content">' . $content . '</div><div class="wall-elem-desc">' . $description . '</div>';
    } else {
      return $content;
    }

  }
  

}