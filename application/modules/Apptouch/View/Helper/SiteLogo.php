<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 08.05.12
 * Time: 18:56
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_View_Helper_SiteLogo extends Zend_View_Helper_Abstract
{
  private $logo = '/application/modules/Apptouch/externals/styles/../images/sitelogo.png';
  private $_path = '/public/apptouch/sitelogo/';
  public function siteLogo()
  {
    $logo = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('apptouch.sitelogo', false);
    if($logo)
      $this->logo = $logo;
    else $this->_path = '';
    return $this;
  }


  public function url($nochache = false){
    return $this->view->baseUrl() . $this->_path . $this->logo . $this->noCache($nochache);
  }
  public function render()
  {
    return $this->htmlImage($this->url(), $this->translate('APPTOUCH_Site Logo'), array('title' => $this->translate('APPTOUCH_Site Logo')));
  }

  private function noCache($nochache = false)
  {
    return $nochache ? '?nocache=' . rand(1, 10000) : '';
  }
}
