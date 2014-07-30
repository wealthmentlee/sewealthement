<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
return array(
  array(
    'title' => 'Welcome Slideshow',
    'description' => 'Displays Welcome Slideshow.',
    'category' => 'Welcome',
    'type' => 'widget',
    'name' => 'welcome.steps',
    'autoEdit' => true,
    'defaultParams' => array(
      'title' => 'Welcome',
      'titleCount' => true,
    ),
    'adminForm' => 'Welcome_Form_Widget_Slideshow'
  ),
) 
?>