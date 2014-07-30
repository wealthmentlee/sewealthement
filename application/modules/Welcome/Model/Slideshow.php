
<?php

  class Welcome_Model_Slideshow extends Core_Model_Item_Abstract
  {
    private $slides = null;
    public $search = false;

    public function getSlides()
    {
      if( $this->slides != null ) return $this->slides;

      $table = Engine_Api::_()->getDbTable('steps','welcome');

      $select = $table->select()
          ->where( 'slideshow_id=?', $this->slideshow_id );

      $this->slides = $table->fetchAll( $select );

      return $this->slides;
    }

    public function getRecommendedSize()
    {

      $width = '';
      $height = '';

      switch( $this->effect ){
        case 'slider':
          $width = $this->width - 22;
          $height = $this->height - 68;
          break;
        case 'tabs':
          $width = $this->width - 40;
          $height = $this->height - 102;
          break;
        case 'popup':
          $width = $this->width - 40 * $this->getSlides()->length;
          $height = $this->height - 22;
          break;
        case 'curtain':
          $width = $this->width - 20;
          $height = $this->height - 20;
          break;
        case 'carousel':
          $width = $this->width;
          $height = $this->height;
          break;
        case 'kenburns':
          $width = $this->width;
          $height = $this->height;
          break;
      }

      return $width . ' X ' . $height . 'px';
    }

    public function getRecommendedWidth()
    {
      if( !$this->effect ) return false;

      switch( $this->effect ){
        case 'slider':
          return $this->width - 22;
        case 'tabs':
          return $this->width - 40;
        case 'popup':
          return $this->width - 40 * $this->getSlides()->length;
        case 'curtain':
          return $this->width - 20;
        case 'carousel':
          return $this->width;
        case 'kenburns':
          return $this->width;
      }
    }

    public function getRecommendedHeight()
    {
      if( !$this->effect ) return false;

      switch( $this->effect ){
        case 'slider':
          return $this->height - 68;
        case 'tabs':
          return $this->height - 102;
        case 'popup':
          return $this->height - 22;
        case 'curtain':
          return $this->height - 20;
        case 'carousel':
          return $this->height;
        case 'kenburns':
          return $this->height;
      }
    }

    public function getTitle(){
      return $this->title;
    }

    public function getHref(){
      return null;
    }

    public function getSetting( $setting_id )
    {
      $table = Engine_Api::_()->getDbTable( 'slideshowSettings', 'welcome' );
      $select = $table->select()
            ->where( 'slideshow_id=?', $this->slideshow_id )
            ->where( 'setting_id=?', $setting_id );

      $setting = $table->fetchRow( $select );

      return $setting;
    }

    // Returns settings accoroding to current animation type from table 'engine4_welcome_slideshowsettings'
    public function getSettings()
    {
      $settings_table = Engine_Api::_()->getDbTable( 'settings', 'welcome' );
      $select = $settings_table->select()
            ->where( 'effect=?', $this->effect );
      $settings = $settings_table->fetchAll( $select );

      $slideshow_settings = array();
      foreach( $settings as $setting ){
        $setting_item = $this->getSetting( $setting->setting_id );
        // if setting exists take this
        if( $setting_item != null ){
          $slideshow_settings[ $setting['name'] ] = $setting_item[ 'value' ];
        }
        // If not take default value
        else{
          $slideshow_settings[ $setting['name'] ] = $setting[ 'value' ];
        }
      }

      return $slideshow_settings;
    }

    public function setSettings( $settings )
    {
      foreach ($settings as $id => $value) {
        $this->setSetting($id, $value);
      }
    }

    public function setSetting( $setting_id, $value )
    {
      $table = Engine_Api::_()->getDbTable('slideshowSettings', 'welcome');
      $select = $table->select();

      $select
        ->where('setting_id = ?', $setting_id)
        ->where('slideshow_id = ?', $this->slideshow_id );
      $slideshow_setting = $table->fetchRow($select);

      if ($slideshow_setting == null) {
        $row = $table->createRow();
        $row->setting_id = $setting_id;
        $row->slideshow_id = $this->slideshow_id;
        $row->value = $value;
        $row->save();

        return $row;
      }else{
        $slideshow_setting->value = $value;
        $slideshow_setting->save();

        return $slideshow_setting;
      }
    }

  }

?>