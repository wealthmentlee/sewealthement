<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 23.11.12
 * Time: 12:23
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_View_Helper_Theme
  extends Zend_View_Helper_Abstract
{
  protected $activeTheme = '';
  protected $staticBaseUrl = '';

  public function theme()
  {
    $activeTheme = Engine_Api::_()->getDbTable('themes', 'apptouch')->getActiveThemeName();
    $staticBaseUrl = $this->view->layout()->staticBaseUrl;
    $rnd = '';
    if(true || APPLICATION_ENV == 'development'){
      $rnd = '?nocache=' . rand(0, 1000);
    }
    return <<<CONTENT
  <link href="{$staticBaseUrl}application/modules/Apptouch/externals/themes/{$activeTheme}/theme.css{$rnd}" media="screen" rel="stylesheet" type="text/css"/>
CONTENT;
  }
}
