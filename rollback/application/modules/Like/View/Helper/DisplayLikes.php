<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: DisplayLikes.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_View_Helper_DisplayLikes extends Engine_View_Helper_HtmlImage
{
  public function displayLikes(Zend_Paginator $likes, $like_count, $host = false, $viewer_liked = false)
  {
    $no_result = $this->view->translate("like_No one like it yet.");

    if ($host){
      $url = 'http://'.$_SERVER['HTTP_HOST'];
    }else{
      $url = '';
    }
      
    if (!$likes->getTotalItemCount() && !$like_count){
      return $no_result;
    }

    $attrs = array('target' => '_blank', 'style' => 'text-decoration: none; font-weight: bold;');

    $viewer = Engine_Api::_()->user()->getViewer();
    $link = $this->view->htmlLink($viewer->getHref(), $this->view->translate('like_You'), $attrs);

    if ($likes->getTotalItemCount() == 0 && $viewer_liked){  
      if ($like_count < 2){
        return $this->view->translate('like_%s like it.', $link);
      }elseif ($like_count == 2){
        return $this->view->translate('like_%s and %s other person like it.', $link, ($like_count-1));
      }else{
        return $this->view->translate('like_%s and %s other people like it.', $link, ($like_count-1));
      }
    }

    if ($likes->getTotalItemCount() == 0 && !$viewer->getIdentity()){
      return $this->view->translate('like_%s other people like it.', $like_count);
    }

    $data = array();
    foreach ($likes as $like){    
      $data[] = $this->view->htmlLink($url.$like->getHref(), $like->getTitle(), $attrs);
    }

    $other_count = $like_count - count($data);
    $output = "";
    if ($viewer_liked){
      $output = $link.', ';
      $other_count--;
    }

    if (count($data) > 0){
      if ($other_count > 1){
        $output .= $this->view->translate('like_%s and %s other people like it.', implode(', ', $data), $other_count);
      }elseif ($other_count == 1){
        $output .= $this->view->translate('like_%s and %s other person like it.', implode(', ', $data), $other_count);
      }else{
        $output .= $this->view->translate('like_%s like it.', implode(', ', $data), $other_count);
      }
    }else{
      if ($other_count > 1){
        $output .= $this->view->translate('like_%s other people like it.', $other_count);
      }else{
        $output .= $this->view->translate('like_%s other person like it.', $other_count);
      }
    }

    return $output;
  }
}