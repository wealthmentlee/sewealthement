<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Widget_MenuFooterController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()
      ->getApi('menus', 'mobile')
      ->getNavigation('core_footer');
    
    // Languages
    $translate    = Zend_Registry::get('Zend_Translate');
    $languageList = $translate->getList();

    //$currentLocale = Zend_Registry::get('Locale')->__toString();

    // Prepare default langauge
    $defaultLanguage = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.locale.locale', 'en');
    if( !in_array($defaultLanguage, $languageList) ) {
      if( $defaultLanguage == 'auto' && isset($languageList['en']) ) {
        $defaultLanguage = 'en';
      } else {
        $defaultLanguage = null;
      }
    }

    // Prepare language name list
    $languageNameList  = array();
    $languageDataList  = Zend_Locale_Data::getList(null, 'language');
    $territoryDataList = Zend_Locale_Data::getList(null, 'territory');

    foreach( $languageList as $localeCode ) {
      $languageNameList[$localeCode] = Zend_Locale::getTranslation($localeCode, 'language', $localeCode);
      if (empty($languageNameList[$localeCode])) {
        list($locale, $territory) = explode('_', $localeCode);
        $languageNameList[$localeCode] = "{$territoryDataList[$territory]} {$languageDataList[$locale]}";
      }
    }
    $languageNameList = array_merge(array(
      $defaultLanguage => $defaultLanguage
    ), $languageNameList);
    $this->view->languageNameList = $languageNameList;
  }

  public function getCacheKey()
  {
    //return true;
  }

  public function setLanguage()
  {

  }
}