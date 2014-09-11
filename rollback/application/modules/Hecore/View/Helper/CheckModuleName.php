<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ItemRate.php 2012-01-12 10:46 alexander $
 * @author     Alexander
 */

class Hecore_View_Helper_CheckModuleName extends Engine_View_Helper_HtmlElement
{
  public function checkModuleName($name)
  {
    switch($name){
      case 'like':
        return 'likes';
        break;

      case 'page':
        return 'pages';
        break;

      case 'pageevent':
        return 'page_events';
        break;

      case 'pagevideo':
        return 'page_videos';
        break;

      case 'pagemusic':
        return 'page_music';
        break;

      case 'pagealbum':
        return 'page_albums';
        break;

      case 'pageblog':
        return 'page_blogs';
        break;

      case 'pagediscussion':
        return 'page_discussions';
        break;

      case 'pagecontact':
        return 'page_contact';
        break;

      case 'pagedocument':
        return 'page_document';
        break;

      case 'pagefaq':
        return 'page_faq';
        break;

      case 'hebadge':
        return 'badges';
        break;

      case 'headvancedalbum':
        return 'advancedalbum';
        break;

      case 'hequestion':
        return 'questions';
        break;

      default:
        return $name;
        break;
    }
  }
}