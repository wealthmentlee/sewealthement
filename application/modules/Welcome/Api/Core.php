<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Welcome_Api_Core extends Core_Api_Abstract
{
  public function getWelcomeSelect($options = array())
  {
    $table = Engine_Api::_()->getItemTable('welcome_step');
    $select = $table->select();

    if( $options['slideshow_id'] == -1 ){
      return $select;
    }

    $select->where( 'slideshow_id=?', $options['slideshow_id'] );

    return $select;
  }

  public function getWelcomePaginator($options = array())
  {
    $paginator = Zend_Paginator::factory($this->getWelcomeSelect($options));
    $paginator->setItemCountPerPage(20);

    return $paginator;
  }

  public function getSlideshowsPaginator()
  {
    $table = Engine_Api::_()->getItemTable('welcome_slideshow');
    $select = $table->select();

    $paginator = Zend_Paginator::factory( $select );
    $paginator->setItemCountPerPage( 10 );

    return $paginator;
  }

  public function getSetting($name, $effect = "", $default = "")
  {
    $name = trim(strtolower($name));
    if ($name == "") {
      return false;
    }
    
    $table = Engine_Api::_()->getDbTable('settings', 'welcome');
    $setting = $table->fetchRow($table->select()->where('name = ?', $name));
    
    if ($setting !== null) {
      return $setting->value;
    }
    
    if ($default !== "") {
      $setting = $table->createRow();
      $setting->value = $default;
      $setting->effect = $effect;
      $setting->save();
    }
    
    return $default;
  }
  
  public function getSettings($effect)
  {
    $effect = trim(strtolower($effect));
    if ($effect == "") {
      return array();
    }
    
    $table = Engine_Api::_()->getDbTable('settings', 'welcome');
    $setting_array = $table->fetchAll($table->select()->where('effect = ?',$effect))->toArray();
    
    $settings = array();
    foreach ($setting_array as $key => $item) {
      if ($item['type'] == 'select' || $item['type'] == 'radio') {
        $item['options'] = unserialize($item['options']);
      }
      $settings[$item['name']] = $item;
    }

    //var_dump( $settings );
    return $settings;
  }
  
  public function setSetting($name, $effect, $value)
  {
    $effect = trim(strtolower($effect));
    $name = trim(strtolower($name));
    
    if (!$name || !$effect ) {
      return false;
    }
    
    $table = Engine_Api::_()->getDbTable('settings', 'welcome');
    $select = $table->select();
    
    $select
      ->where('name = ?', $name)
      ->where('effect = ?', $effect);
    $setting = $table->fetchRow($select);
    
    if ($setting == null) {
      return ;
    }
    
    $setting->value = $value;
    $setting->save();
  }
  
  public function setSettings(array $settings, $effect)
  {
    $effect = trim(strtolower($effect));
    if (empty($settings) || !$effect) {
      return false;
    }
    
    foreach ($settings as $name => $value) {
      $this->setSetting($name, $effect, $value);
    }
  }
  
  public function displayOptions(array $settings)
  {
    $option_str = array();
    foreach($settings as $name => $value) {
      $str = $name . ": ";
      if (is_numeric($value) || $value == 'true' || $value == 'false') {
        $str .= $value;
      } else {
        $str .= "'".$value."'";
      }
      $option_str[] = $str;
    }
    
    return implode(',', $option_str);
  }
}