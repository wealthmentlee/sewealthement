<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallFluentList.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_View_Helper_WallFluentList extends Zend_View_Helper_Abstract
{

  public function wallFluentList($items, $translate = false)
  {
    if( 0 === ($num = count($items)) )
    {
      return '';
    }
    
    $comma = $this->view->translate(',');
    $and = $this->view->translate('and');
    $index = 0;
    $content = '';
    foreach( $items as $item )
    {
      if( $num > 2 && $index > 0 ) $content .= $comma . ' '; else $content .= ' ';
      if( $num > 1 && $index == $num - 1 ) $content .= $and . ' ';

      $href = null;
      $title = null;
      $guid = null;

      if( is_object($item) ) {
        if( method_exists($item, 'getTitle') && method_exists($item, 'getHref') ) {
          $href = $item->getHref();
          $title = $item->getTitle();
        } else if( method_exists($item, '__toString') ) {
          $title = $item->__toString();
        } else {
          $title = (string) $item;
        }

        if (method_exists($item, 'getGuid')){
          $guid = $item->getGuid();
        }


      } else {
        $title = (string) $item;
      }
      
      if( $translate ) {
        $title = $this->view->translate($title);
      }

      if( null === $href ) {
        $content .= $title;
      } else {
        $content .= $this->view->htmlLink($href, $title, array('class' => 'wall_liketips', 'rev' => $guid));
      }
      
      $index++;
    }
    
    return $content;
  }
}