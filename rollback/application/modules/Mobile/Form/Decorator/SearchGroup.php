<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: SearchGroup.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    

class Engine_Form_Decorator_SearchGroup extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;
  
  public function render($content)
  {
		$options = $this->getOptions();
		if (isset($options['search']) && $options['search'])
		{
			return '<td class="search_text">' . $content . '</td>';
		} elseif (isset($options['submit']) && $options['submit']) {
			return '<td class="search_submit">' . $content . '</td>';
		} elseif (isset($options['group']) && $options['group']){
			return '<table class="search"><tr>' . $content . '</tr></table>';
		}
		
    return $content;
  }
}