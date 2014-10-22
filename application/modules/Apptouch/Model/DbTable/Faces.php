<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 23.07.13
 * Time: 15:22
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Model_DbTable_Faces extends Engine_Db_Table
{
  protected $_rowClass = 'Apptouch_Model_Faces';
  protected $urls = array();
  protected $detector;
  public function __construct($config = array())
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    if($key = $settings->getSetting('apptouch.sky.biometry.key'))
      $this->detector = new Apptouch_FCClientPHP($key, $settings->getSetting('apptouch.sky.biometry.secret'));
    parent::__construct($config);
  }

  public function getFaces($data, $detect = true){
    if($data instanceof Core_Model_Item_Abstract && (strpos($data->getType(), 'photo') !== false)){
/**
 * @var $data Core_Model_Item_Abstract
*/
      if($faces = $this->getRowByItem($data)){
        return $faces->parse();
      } elseif($detect){
        return $this->detect($data);
      }
    } elseif($data instanceof Zend_Paginator){
//       todo Implement
    }
  }

  public function detect($data)
  {
    $isLocal = self::isLocal();
    $filename = null;
    $first = null;
    if ($data instanceof Zend_Paginator) {
      /**
       * @var $data Zend_Paginator
       */
      $first = $data->getItem(1, 1);
    } else {
      $first = $data;
      $data = array($data);
    }

    if ($first instanceof Core_Model_Item_Abstract && (strpos($first->getType(), 'photo') !== false) && isset($first->file_id)) {

      if ($isLocal)
        $filename = self::getFilePath($first);
      else
        foreach ($data as $item) {
          /**
           * @var $item Core_Model_Item_Abstract
           */
          $this->urls[] = self::getFileUrl($item);
        }
      $response = null;
      if ($filename)
        $response = $this->detector->faces_detect(null, $filename, null, null, 'Aggressive', 'all');
      elseif (count($this->urls))
        $response = $this->detector->faces_detect($this->urls, null, null, null, 'Aggressive', 'all');

      if (@$response->photos) {
        $counter = 0;
        $r_photos = $response->photos;
        $db = $this->getAdapter();
        $db->beginTransaction();
        try
        {
          foreach ($data as $item) {
            $row = $this->getRowByItem($item, true);
            if(!isset($r_photos[$counter])){
                $counter++;
              continue;
            }
            $row->data = Zend_Json::encode($r_photos[$counter]);
            $row->save();
            $counter++;
          }
          $db->commit();
        }
        catch (Exception $e)
        {
          $db->rollBack();
          throw $e;
        }
        return $this->parse($r_photos);
      }
    }

  }

  private static function isLocal(){
    return strpos($_SERVER['SERVER_ADDR'], '192.168.') === 0;
  }
  private static function getFileUrl(Core_Model_Item_Abstract $item){
      $file_id = $item->file_id;
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id);
      if($file)
        return rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']) . $file->getPhotoUrl();
  }

  private static function getFilePath(Core_Model_Item_Abstract $item){
      $file_id = $item->file_id;
      $file = Engine_Api::_()->getItemTable('storage_file')->getFile($file_id);
      if($file)
        return APPLICATION_PATH . DIRECTORY_SEPARATOR . $file->storage_path;
  }
/**
 *  @return Apptouch_Model_Faces
*/
  public function getRowByItem($item, $create = false)
  {
    if(isset($item->file_id)){
      $file_id = $item->file_id;
      $select = $this->select()
          ->where('file_id = ?', $file_id)
          ->limit(1);
      $row = $this->fetchRow($select);
      if($create && !$row){
        $row = $this->createRow();
        $row->file_id = $item->file_id;
      }
      return $row;
    }

  }
  private function parse($photos){
    $photosFormat = array();
    if(isset($photos['tags']) || isset($photos->tags))
      $photos = array($photos);
    if(is_array($photos[0])){
      foreach($photos as $photo){
        $photoFormat = array();
        $wpk = $photo['width'] / 100;
        $hpk = $photo['height'] / 100;
        $photoFormat['width'] = $photo['width'];
        $photoFormat['height'] = $photo['height'];
        $tagsFormat = array();
        foreach($photo['tags'] as $tag){
          $tagFormat = array();
          $w = $tag['width'] * $wpk;
          $h = $tag['height'] * $hpk;
          $tagFormat['x'] = $tag['center']['x'] * $wpk - $w / 1.197;
          $tagFormat['y'] = $tag['center']['y'] * $hpk - $h / 1.197;
          $tagFormat['width'] = $w * 1.67;
          $tagFormat['height'] = $h * 1.67;
          $tagsFormat[] = $tagFormat;
        }
        $photoFormat['tags'] = $tagsFormat;
        $photosFormat[] = $photoFormat;
      }
    }else{
      foreach($photos as $photo){
        $photoFormat = array();
        $wpk = $photo->width / 100;
        $hpk = $photo->height / 100;
        $photoFormat['width'] = $photo->width;
        $photoFormat['height'] = $photo->height;
        $tagsFormat = array();
        foreach($photo->tags as $tag){
          $tagFormat = array();
          $w = $tag->width * $wpk;
          $h = $tag->height * $hpk;
          $tagFormat['x'] = $tag->center->x * $wpk - $w / 1.197;
          $tagFormat['y'] = $tag->center->y * $hpk - $h / 1.197;
          $tagFormat['width'] = $w * 1.67;
          $tagFormat['height'] = $h * 1.67;
          $tagsFormat[] = $tagFormat;
        }
        $photoFormat['tags'] = $tagsFormat;
        $photosFormat[] = $photoFormat;
      }
    }
    return count($photosFormat) == 1 ? $photosFormat[0] : $photosFormat;
  }
}
