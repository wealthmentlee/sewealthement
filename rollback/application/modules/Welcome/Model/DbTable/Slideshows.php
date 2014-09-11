
<?php

  class Welcome_Model_DbTable_Slideshows extends Engine_Db_Table
  {
    protected $_rowClass = 'Welcome_Model_Slideshow';

    public function getArray(){
      $slideshows = $this->fetchAll();

      $array = array();
      foreach( $slideshows as $slideshow ){
        $array[$slideshow->slideshow_id] = $slideshow->title;
      }

      return $array;
    }
  }

?>