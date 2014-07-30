<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: MobileItemRate.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_View_Helper_MobileItemRate extends Engine_View_Helper_HtmlElement
{
  public function mobileItemRate($item_type, $item_id, $show_score = false, $score_br = true)
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('rate')){
      return '';
    }
    if (!Engine_Api::_()->getApi('settings', 'core')->getSetting('mobile.show.rate-browse', 1)){
      return '';
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $translate = Zend_Registry::get('Zend_Translate');

    //$maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
    $maxRate = 5;// todo change stars count

    if ($item_type == 'page'){

      $rate_info = Engine_Api::_()->getDbTable('pagereviews', 'rate')->getScore($item_id);

      if (!is_array($rate_info)){ return false; }

      $stars_str = '<div class="rate_stars_cont">';
      $star_value = 'no_rate';

      for ($i = 0; $i < $maxRate; $i++)
      {
        if (($i + 0.125) > $rate_info['item_score']) {
          $star_value = 'no_rate';
        } else if (($i + 0.375) > $rate_info['item_score']) {
          $star_value = 'quarter_rated';
        } else if (($i + 0.625) > $rate_info['item_score']) {
          $star_value = 'half_rated';
        } else if (($i + 0.875) > $rate_info['item_score']) {
          $star_value = 'fquarter_rated';
        } else {
          $star_value = 'rated';
        }

        $stars_str .= $this->view->htmlImage($this->view->baseUrl() . '/application/modules/Mobile/externals/images/rate/small_' . $star_value . '.png');
      }

      $stars_str .= '</div>';

      if ($score_br){ $br = '<br />'; } else { $br = ''; }

      $count_review = false;

      if ($rate_info['count']) {
        $count_review = $this->view->translate(array('rate_%s review', 'rate_%s reviews', $rate_info['count']), '<b>'.$rate_info['count'].'</b>');
      }

      $score_str = '<div class="he_rate_small_cont"><div class="rate_stars_cont">'.$stars_str;

      if ($show_score) {
        $score_str .= '</div></div><div class="item_rate_info">' . $translate->_('Score:') . ' <b>' . $rate_info['item_score'] . ' / ' . $maxRate . '</b><br/>' . $count_review . $br;
        $score_str .= '</div><div class="clr"></div>';
      } else {
        $score_str .= '<div class="count">'.$count_review.'</div><div class="clr"></div>';
        $score_str .= '</div></div><div class="clr"></div>';
      }

      return $score_str;

    } else {

      $rate_info = Engine_Api::_()->getDbtable('rates', 'rate')->fetchRateInfo($item_type, $item_id);
      $item_score = ($rate_info && $rate_info['rate_count']) ? $rate_info['total_score'] / $rate_info['rate_count'] : 0;
      $item_score = round($item_score, 2);

      if ($score_br) {
        $br = '<br />';
      } else {
        $br = '';
      }

    }

    $stars_str = '';
    $star_value = 'no_rate';

    for ($i = 0; $i < $maxRate; $i++)
    {
      if (($i + 0.125) > $item_score) {
        $star_value = 'no_rate';
      } else if (($i + 0.375) > $item_score) {
        $star_value = 'quarter_rated';
      } else if (($i + 0.625) > $item_score) {
        $star_value = 'half_rated';
      } else if (($i + 0.875) > $item_score) {
        $star_value = 'fquarter_rated';
      } else {
        $star_value = 'rated';
      }

      $stars_str .= $this->view->htmlImage($this->view->baseUrl() . '/application/modules/Mobile/externals/images/rate/small_' . $star_value . '.png');
    }

    $score_str = '';
    if ( $show_score ) {
      $score = ($rate_info['rate_count']) ? $rate_info['rate_count'] : 0;
      $vote_lang_var = $translate->_(array('vote', 'votes', (($rate_info['rate_count']) ? $rate_info['rate_count'] : 0)));

      $score_str = '
      <div class="item_rate_info">
        ' . $translate->_('Score:') . '  <span class="item_score">' . $item_score . ' / ' . $maxRate . ' </span> ' . $br . '
        <span class="item_votes"> ' . $score . '</span> ' . $vote_lang_var .
      '</div>';
    }

    return '<div class="he_rate_small_cont"><div class="rate_stars_cont">' . $stars_str .'</div></div><div class="clr"></div>' . $score_str ;


  }
}