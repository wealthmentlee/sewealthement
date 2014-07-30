<?php

  class Welcome_KenburnsController extends Core_Controller_Action_Standard
  {
    public function indexAction()
    {
      $slideshow_id = $this->_getParam( 'slideshow_id', 1 );

      $this->view->paginator = $paginator = Engine_Api::_()->welcome()->getWelcomePaginator( array('slideshow_id' => $slideshow_id) );

      $slideshow = Engine_Api::_()->getItem('welcome_slideshow', $slideshow_id );

      if( !$slideshow->width || !$slideshow->height ){
        $this->view->width = Engine_Api::_()->getApi('settings', 'core')->getSetting('welcome.width', 900);
        $this->view->height = Engine_Api::_()->getApi('settings', 'core')->getSetting('welcome.height', 150);
      }else{
        $this->view->width = $slideshow->width;
        $this->view->height = $slideshow->height;
      }

      $this->view->settings = $slideshow->getSettings();

      // Html attributes
      $this->view->containerId = 'kenburns_' . $slideshow_id;

      // list image paths for js
      $data = '{';
      $firstImage = '';

      $i = 0;
      foreach( $paginator as $slide ){
        $data .= " '" . $slide->getPhotoUrl() ."': " . "{caption: '" . $slide->getBody() . "',href: '" . $slide->link . "'},";
        if( $i == 0 ) $firstImage = $slide->getPhotoUrl();
        $i++;
      }
      $data .= "}";

      $this->view->data = $data;
      $this->view->firstImage = $firstImage;
    }
  }

?>