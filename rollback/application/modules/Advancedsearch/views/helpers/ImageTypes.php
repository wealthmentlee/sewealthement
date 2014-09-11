<?php
/***/
class Advancedsearch_View_Helper_ImageTypes extends Zend_View_Helper_Abstract {
  public function imageTypes($type)
  {
    $types = array(
      'pageblog' => 'thumb.profile',
      'event' => 'thumb.profile',
      'pageevent' => 'thumb.profile',
      'store_product' => 'thumb.profile',
      'group' => 'thumb.profile',
      'music_playlist' => 'thumb.profile',
      'playlist' => 'thumb.profile',
      'page' => 'thumb.profile',
      'user' => 'thumb.profile',
    );
    if (isset($types[$type])) {
      return 'thumb.profile';
    } else {
      return 'thumb.normal';
    }
  }
}