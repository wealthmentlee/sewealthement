<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



$data = array(
  array(
    'title' => 'Wealthment - Wall Feed',
    'description' => 'Displays the activity feed.',
    'category' => 'Wealthment',
    'type' => 'widget',
    'name' => 'wealthment.feed',
    'defaultParams' => array(
      'title' => 'What\'s New',
    ),
    'adminForm' => array(
        'elements' => array(
        array(
          'Select',
          'cat',
          array(
            'label' => 'Category',
            'multiOptions' => array(
                '0' => 'All',
                '1' => 'Stocks',
            '2' => 'Real Estate',
            '3' => 'Retirement',
            '4' => 'Others'
            )  
          )
        ),
      )
    )  
  ),
  array(
    'title' => 'Wealthment - Search Wall Feed',
    'description' => 'Displays the activity feed according to search.',
    'category' => 'Wealthment',
    'type' => 'widget',
    'name' => 'wealthment.search-feed',
  ),
  array(
    'title' => 'Wealthment - Follow Feeds',
    'description' => 'Displays the activity feed according to follows.',
    'category' => 'Wealthment',
    'type' => 'widget',
    'name' => 'wealthment.followfeed',
  ),
);
  

return $data; ?>