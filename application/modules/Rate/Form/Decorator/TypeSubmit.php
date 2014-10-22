<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: TypeSubmit.php 2010-07-02 19:47 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Engine_Form_Decorator_TypeSubmit extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;

  public function render($element)
  {
    $element1 = $this->getOption('element1');
    $element2 = $this->getOption('element2');

    if (!$element1){
      $element1 = $element;
    } else if (!$element2){
      $element2 = $element;
    }

    return '<div class="element1">'.$element1.'</div>
      <div class="element2">'.$element2.'</div>
      <div style="clear:both;"></div>';

  }
}