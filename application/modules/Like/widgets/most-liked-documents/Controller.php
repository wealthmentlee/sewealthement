<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Controller
 *
 * @author adik
 */
class Like_Widget_MostLikedDocumentsController extends Engine_Content_Widget_Abstract{
  public function indexAction()
  {
    $this->view->item_type = $item_type = 'document';
    $api = Engine_Api::_()->getApi('core','like');

    $settings = Engine_Api::_()->getApi('settings','core');
    $ipp = $settings->getSetting('like.document.count',9);
    $this->view->period = $period = $settings->getSetting('like.document.period',1);

    $likes = $api->getMostLikedData($item_type,$ipp);

    if(!count($likes))
    {
      return $this->setNoRender();
    }

    if($period)
    {
      $this->view->week_likes = $week_likes = $api->getMostLikedData($item_type,$ipp,'week');
      $this->view->month_likes = $month_likes = $api->getMostLikedData($item_type,$ipp,'month');
    }

    $this->view->all_likes = $all_likes = $likes;

    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('like');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $this->getElement()->setAttrib('class', 'like_widget_theme_' . $this->view->activeTheme());
  }
}

?>
