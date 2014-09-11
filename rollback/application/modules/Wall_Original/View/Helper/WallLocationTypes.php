<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallLocationTypes.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_View_Helper_WallLocationTypes extends Zend_View_Helper_Abstract
{
  public function wallLocationTypes($types)
  {
    $types = explode(',', $types);

    $place_types = array(
      'country' => 'in ',
      'locality' => 'in ',
      'natural_feature' => 'in ',
      'room' => 'in ',
      'route' => 'on ',
      'street_address' => 'on ',
      'street_number' => 'on ',
      'sublocality' => 'in ',
      'sublocality_level_4' => 'in ',
      'sublocality_level_5' => 'in ',
      'sublocality_level_3' => 'in ',
      'sublocality_level_2' => 'in ',
      'sublocality_level_1' => 'in '
    );

    return isset($place_types[$types[0]]) ? $place_types[$types[0]] : 'at ';
  }
}