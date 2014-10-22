<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 08.05.12
 * Time: 18:56
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_View_Helper_HomeScreen extends Zend_View_Helper_Abstract
{
  private $_path = '/public/apptouch/homescreen/';
  private $_original = 'original.';
  private $_57x57 = '57x57.';
  private $_114x114 = '114x114.';
  private $_144x144 = '144x144.';
  private $_extension = '144x144.';

  public function homeScreen()
  {
    return $this->hasHomeScreen() ? $this : false;
  }

  public function getLinkOriginal($nochache = false)
  {
    return $this->view->baseUrl() . $this->_path . $this->_original . $this->_extension . $this->noCache($nochache);
  }

  public function getLink57x57($nochache = false)
  {
    return $this->view->baseUrl() . $this->_path . $this->_57x57 . $this->_extension . $this->noCache($nochache);
  }

  public function getLink114x114($nochache = false)
  {
    return $this->view->baseUrl() . $this->_path . $this->_114x114 . $this->_extension . $this->noCache($nochache);
  }

  public function getLink144x144($nochache = false)
  {
    return $this->view->baseUrl() . $this->_path . $this->_144x144 . $this->_extension . $this->noCache($nochache);
  }

  private function hasHomeScreen($nochache = false)
  {
    $hs = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('apptouch.homescreen.extension', false);
    if ($hs !== false) {
      $this->_extension = $hs;
      $hs = true;
    }
    return $hs;
  }

  public function render()
  {
    if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('apptouch.homescreen.enabled', false))
      return '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="' . $this->getLink144x144() . '">' .
        // For android
        '<link rel="icon" sizes="114x114" href="' . $this->getLink57x57() . '">' .
        '<link rel="shortcut icon" sizes="114x114" href="' . $this->getLink114x114() . '">

      <link rel="apple-touch-icon-precomposed" sizes="114x114" href="' . $this->getLink114x114() . '">
      <link rel="apple-touch-icon-precomposed" sizes="57x57" href="' . $this->getLink57x57() . '">
      <link rel="apple-touch-icon-precomposed" href="' . $this->getLink114x114() . '">';
  }

  private function noCache($nochache = false)
  {
    return $nochache ? '?nocache=' . rand(1, 10000) : '';
  }
}
