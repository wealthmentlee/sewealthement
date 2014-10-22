<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Bootstrap.php 2010-07-02 19:27 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);

    $this->initViewHelperPath();

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $front_router = Zend_Controller_Front::getInstance()->getRouter();

    $plugins_settings = Zend_Json::encode( array(
      'blog' => array(
        'enabled' => $settings->getSetting('rate.blog.enabled', true),
        'url_rate' => $front_router->assemble(array('module' => 'rate'), 'getRateContainer')
      ),
      'album' => array(
        'enabled' => $settings->getSetting('rate.album_photo.enabled', true),
        'url_rate' => $front_router->assemble(array('module' => 'rate'), 'getRateContainer')
      ),
      'article' => array(
        'enabled' => $settings->getSetting('rate.article.enabled', true),
        'url_rate' => $front_router->assemble(array('module' => 'rate'), 'getRateContainer')
      )
    ));

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile('application/modules/Rate/externals/scripts/Rate.js');
    $headScript->appendScript('getRateContainer(' . $plugins_settings . ');');
  }
}