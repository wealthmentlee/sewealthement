<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 06.09.12
 * Time: 14:48
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_View_Helper_LocaleFormats extends Zend_View_Helper_Abstract
{
  public function localeFormats()
  {
    return $this;
  }
  public function render()
  {
    $locale = '<locale ' .
      'date="' . $this->date() . '"' .
      '></locale>
    ';
    return $locale;
  }
  public function date()
  {
    $localeObject = Zend_Registry::get('Locale');
    $dateLocaleString = $localeObject->getTranslation('long', 'Date', $localeObject);
    $dateLocaleString = preg_replace('~\'[^\']+\'~', '', $dateLocaleString);
    $dateLocaleString = strtolower($dateLocaleString);
    $dateLocaleString = preg_replace('/[^ymd]/i', '', $dateLocaleString);
    return str_replace('//', '', preg_replace(array('/y+/i', '/m+/i', '/d+/i'), array('yy/', 'mm/', 'dd/'), $dateLocaleString) . '/');
  }
}
