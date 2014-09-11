<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: LikePhoto.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_View_Helper_LikePhoto extends Engine_View_Helper_HtmlImage
{
  protected $_noPhotos;
  
  public function likePhoto($item, $type = 'thumb.profile', $alt = "", $attribs = array())
  {
    // Whoops
    if( !($item instanceof Core_Model_Item_Abstract))
    {
      throw new Zend_View_Exception("Item must be a valid item");
    }

    // Get url
    $src = $item->getPhotoUrl($type);
    $safeName = ( $type ? str_replace('.', '_', $type) : 'main' );
    $attribs['class'] = ( isset($attribs['class']) ? $attribs['class'] . ' ' : '' );
    $attribs['class'] .= $safeName . ' ';
    $attribs['class'] .= 'item_photo_' . $item->getType() . ' ';

    // User image
    if( $src )
    {
      // Add auto class and generate
      $attribs['class'] = ( !empty($attribs['class']) ? $attribs['class'].' ' : '' ) . $safeName;
    }

    // Default image
    else
    {
      $src = $this->view->baseUrl()."/".$this->getNoPhoto($item, $safeName);
      $attribs['class'] .= 'item_nophoto ';
    }

    return $this->htmlImage($src, $alt, $attribs);
  }

  public function getNoPhoto($item, $type)
  {
    if( ($item instanceof Core_Model_Item_Abstract) ) {
      $item = $item->getType();
    } else if( !is_string($item) ) {
      return '';
    }
    
    if( !Engine_Api::_()->hasItemType($item) ) {
      return '';
    }

    // Load from registry
    if( null === $this->_noPhotos ) {
      // Process active themes
      $themesInfo = Zend_Registry::get('Themes');
      foreach( $themesInfo as $themeName => $themeInfo ) {
        if( !empty($themeInfo['nophoto']) ) {
          foreach( (array)@$themeInfo['nophoto'] as $itemType => $moreInfo ) {
            if( !is_array($moreInfo) ) continue;
            $this->_noPhotos[$itemType] = array_merge((array)@$this->_noPhotos[$itemType], $moreInfo);
          }
        }
      }
    }
    
    // Use default
    if( !isset($this->_noPhotos[$item][$type]) ) {
      $shortType = $item;
      if( strpos($shortType, '_') !== false ) {
        list($null, $shortType) = explode('_', $shortType, 2);
      }
      $module = Engine_Api::_()->inflect(Engine_Api::_()->getItemModule($item));
      $this->_noPhotos[$item][$type] = //$this->view->baseUrl() . '/' .
        'application/modules/' .
        $module .
        '/externals/images/nophoto_' .
        $shortType . '_'
        . $type . '.png';
    }

    return $this->_noPhotos[$item][$type];
  }
}