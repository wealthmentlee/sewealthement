<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 23.07.13
 * Time: 15:25
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Model_Faces extends Engine_Db_Table_Row
{
  public function parse(){
    if(!$this->parsed){
      $photo = Zend_Json::decode($this->data);
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

      $this->parsed = Zend_Json::encode($photoFormat);
    } else $photoFormat = Zend_Json::decode($this->parsed);
    $this->save();
    return $photoFormat;
  }

}
