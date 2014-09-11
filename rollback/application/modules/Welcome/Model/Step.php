<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Step.php 2010-08-02 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Welcome_Model_Step extends Core_Model_Item_Abstract
{
  protected $_collectible_type = "welcome_step";
  public $search = false;
  
  public function getHref()
  {
    return null;
  }
  
  public function getTitle()
  {
    return $this->title;
  }
  
  public function getBody()
  {
    return $this->body;
  }
  
  public function setPhoto($photo)
  {
    if ( $photo instanceof Zend_Form_Element_File ) {
      $file = $photo->getFileName();
    } else if ( $photo instanceof Storage_Model_File ) {
      $file = $photo->temporary();
    } else if ( $photo instanceof Core_Model_Item_Abstract && !empty($photo->photo_id) ) {
      $file = Engine_Api::_()->getItem('storage_file', $photo->photo_id)->temporary();
    } else if ( is_array($photo) && !empty($photo['tmp_name']) ) {
      $file = $photo['tmp_name'];
    } else if ( is_string($photo) && file_exists($photo) ) {
      $file = $photo;
    } else {
      throw new User_Model_Exception('invalid argument passed to setPhoto');
    }


    $slideshow = Engine_Api::_()->getItem( 'welcome_slideshow', $this->slideshow_id );
    
    $width = $slideshow->getRecommendedWidth();
    $height = $slideshow->getRecommendedHeight();
    
    $name = basename($file);
      
    $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary' . DIRECTORY_SEPARATOR . 'welcome';
    
    if (!is_dir($path)) {
      mkdir($path);
    }
    
    $params = array(
      'parent_type' => 'welcome',
      'parent_id' => $this->getIdentity()
    );
    
    $storage = Engine_Api::_()->storage();
    $this->deletePhoto();
    
    // Resize image (main)
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize($width, $height) // TODO : Change the sizes of the image
      ->write($path.'/'.$name)
      ->destroy();
    
    $image = Engine_Image::factory();
    $image->open($file)
      ->resize(60, 60) // TODO : Change the sizes of the thumb image
      ->write($path . '/t' . $name)
      ->destroy();
    
    $iMain = $storage->create($path . '/' . $name, $params);
    $iThumb = $storage->create($path . '/t' . $name, $params);
    
    $iMain->bridge($iThumb, 'thumb.normal');
    
    $this->photo_id = $iMain->file_id;
    $this->save();
    
    @unlink($path . '/' . $name);
    @unlink($path . '/t' . $name);
  }
  
  public function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false){
      if ($length == 0)
          return '';
  
      if (strlen($string) > $length) {
          $length -= strlen($etc);
          if (!$break_words && !$middle) {
              $string = preg_replace('/\s+?(\S+)?$/', '', Engine_String::substr($string, 0, $length+1));
          }
          if(!$middle) {
              return Engine_String::substr($string, 0, $length).$etc;
          } else {
              return Engine_String::substr($string, 0, $length/2) . $etc . Engine_String::substr($string, -$length/2);
          }
      } else {
          return $string;
      }
  }
  
  public function deletePhoto() 
  {
    if (!$this->photo_id) {
      return false;
    }
    
    $storage = Engine_Api::_()->storage();
    
    $file = $storage->get($this->photo_id);
    $file->remove();
    
    $file = $storage->get($this->photo_id, 'thumb.normal');
    $file->remove();
    
    return true;
  }
  
  public function deleteStep()
  {
    $this->deletePhoto();
    parent::delete();
  }
}