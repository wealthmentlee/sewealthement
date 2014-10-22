<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TypeTip.php 2010-07-02 19:47 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_TypeTip extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;

  public function render($element)
  {
    return '<div class="tip"><span>'.$this->getOption('text').'</span></div>';
  }
}