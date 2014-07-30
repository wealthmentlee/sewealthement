<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: MobileFormRate.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Engine_Form_Decorator_MobileFormRate extends Zend_Form_Decorator_Abstract
{
  protected $_placement = null;

  public function render($element)
  {
    $maxRate = 5;

    $html = '';

    $element_name = $this->getElement()->getName();
    $element_value = $this->getElement()->getValue();

    for ($i=0; $i<$maxRate; $i++){
      $html .= '
        <input name="'.$element_name.'" type="radio" value="'.($i+1).'" class="mobile_form_rate_radio" '.(($element_value == $i+1) ? 'CHECKED' : '').'/>
        <div class="mobile_form_rate_div">'.($i+1).'</div>
      ';
    }

    $html .= '<div style="clear:both;"></div>';

    return '
      <div id="title-wrapper" class="form-wrapper">
        <div id="title-label" class="form-label">
            <label class="optional">'.$this->getElement()->getLabel().'</label>
        </div>
        <div id="title-element" class="form-element">'.$html.'</div>
      </div>
    ';

  }

}