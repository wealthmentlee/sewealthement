<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: LikeLogo.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_LikeLogo extends Zend_Form_Decorator_Abstract
{
  /**
   * Render
   *
   * Renders as the following:
   * <dt></dt>
   * <dd>$content</dd>
   *
   * @param  string $content
   * @return string
   */
  public function render($content)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $storage = Engine_Api::_()->storage();

    $file_id = $settings->getSetting('like.logo');
    $base_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'home');

    $no_photo_icon = $base_url . "application/modules/Like/externals/images/nophoto/icon.png";

    if (!$file_id){
      $icon = $no_photo_icon;
    }else{
      $file = $storage->get($file_id);
      if ($file !== null){
        $icon = $file->map();
      }else{
        $icon = $no_photo_icon;
      }
    }

    return '<div class="like_logo">'
      . '<div class="like_logo_input">' . $content . '</div>'
      . '<div class="like_logo_icon"><img src="' . $icon . '" /></div>'
      . '</div>';
  }
}