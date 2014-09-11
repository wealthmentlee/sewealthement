<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: MobileContent.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_View_Helper_MobileContent extends Zend_View_Helper_Abstract
{
	protected $_name;
	
  public function mobileContent($name)
  {
    // Direct access
    if( func_num_args() == 0 )
    {
      return $this;
    }

    if( func_num_args() > 1 )
    {
      $name = func_get_args();
    }

    $content = Engine_Content::getInstance();

		$table = Engine_Api::_()->getDbtable('pages', 'mobile');

		$content->setStorage($table);
		
    return $content->render($name);
  }

  public function renderWidget($name)
  {
    $structure = array(
      'type' => 'widget',
      'name' => $name,
    );

    // Create element (with structure)
    $element = new Engine_Content_Element_Container(array(
      'elements' => array($structure),
      'decorators' => array(
        'Children'
      )
    ));

    return $element->render();
  }
}