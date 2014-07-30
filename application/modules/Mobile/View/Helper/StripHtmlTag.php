<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: StripHtmlTag.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_View_Helper_StripHtmlTag extends Zend_View_Helper_Abstract
{
  public function stripHtmlTag($str, $tags, $stripContent = true)
  {
    $content = '';

    if (!is_array($tags)) {
      $tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
      if (end($tags) == '') {
        array_pop($tags);
      }
    }

    foreach ($tags as $tag) {
      if ($stripContent) {
        $content = '(.+</'.$tag.'[^>]*>|)';
      }
      $str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '', $str);
    }

    return $str; 
  }
}
