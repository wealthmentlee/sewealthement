<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Url.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_Helper_Url extends Activity_Model_Helper_Abstract
{
  /**
   * Generates a url for action
   * 
   * @param mixed $params
   * @param string $innerHTML
   * @return string
   */
  public function direct($params, $innerHTML)
  {
    // Passed an absolute url
    if( is_string($params) )
    {
      $uri = $params;
    }
    
    else if( is_array($params) && isset($params['uri']) )
    {
      $uri = $params['uri'];
    }

    // Passed a route array
    else if( is_array($params) )
    {
      $route = ( isset($params['route']) ? $params['route'] : 'default' );
      unset($params['route']);
      $uri = Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
    }

    // Whoops, just return the innerHTML
    else
    {
      return $innerHTML;
    }

    return '<a href="'.$uri.'">'.$innerHTML.'</a>';
  }
}