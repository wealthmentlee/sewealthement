<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ItemRate.php 2010-07-02 19:53 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Rate_View_Helper_ItemRate extends Engine_View_Helper_HtmlElement
{
  public function itemRate($item_type, $item_id, $show_score = false, $score_br = true, $period = 'all')
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $translate = Zend_Registry::get('Zend_Translate');

    //$maxRate = $settings->getSetting('rate.' . $item_type . '.max.rate', 5);
    $maxRate = 5; // todo change stars count

    if ($item_type == 'page') {
      $rate_info = Engine_Api::_()->getDbTable('pagereviews', 'rate')->getScore($item_id);

      if (!is_array($rate_info)) {
        return false;
      }

      $stars_str = $this->view->reviewRate($rate_info['item_score'], true);

      if ($score_br) {
        $br = '<br />';
      } else {
        $br = '';
      }

      $count_review = false;

      if ($rate_info['count']) {
        $count_review = $this->view->translate(array('rate_%s review', 'rate_%s reviews', $rate_info['count']), '<b>' . $rate_info['count'] . '</b>');
      }

      $score_str = '<div class="he_rate_small_cont"><div class="rate_stars_cont">' . $stars_str;

      if ($show_score) {
        $score_str .= '</div></div><div class="item_rate_info">' . $translate->_('Score:') . ' <b>' . $rate_info['item_score'] . ' / ' . $maxRate . '</b><br/>' . $count_review . $br;
        $score_str .= '</div><div class="clr"></div>';
      } else {
        $score_str .= '<div class="pagereview_count">' . $count_review . '</div>';
        $score_str .= '</div></div><div class="clr"></div>';
      }

      return $score_str;

    } elseif ($item_type == 'offer') {
        $rate_info = Engine_Api::_()->getDbTable('offerreviews', 'rate')->getScore($item_id);

        if (!is_array($rate_info)) {
          return false;
        }

        $stars_str = $this->view->reviewRate($rate_info['item_score'], true);

        if ($score_br) {
          $br = '<br />';
        } else {
          $br = '';
        }

        $count_review = false;

        if ($rate_info['count']) {
          $count_review = $this->view->translate(array('rate_%s review', 'rate_%s reviews', $rate_info['count']), '<b>' . $rate_info['count'] . '</b>');
        }

        $score_str = '<div class="he_rate_small_cont"><div class="rate_stars_cont">' . $stars_str;

        if ($show_score) {
          $score_str .= '</div></div><div class="item_rate_info">' . $translate->_('Score:') . ' <b>' . $rate_info['item_score'] . ' / ' . $maxRate . '</b><br/>' . $count_review . $br;
          $score_str .= '</div><div class="clr"></div>';
        } else {
          $score_str .= '<div class="offerreview_count">' . $count_review . '</div>';
          $score_str .= '</div></div><div class="clr"></div>';
        }

        return $score_str;

      } else {

      $rate_info = Engine_Api::_()->getDbtable('rates', 'rate')->fetchRateInfo($item_type, $item_id, $period);
      $item_score = ($rate_info && $rate_info['rate_count']) ? $rate_info['total_score'] / $rate_info['rate_count'] : 0;
      $item_score = round($item_score, 2);

      if ($score_br) {
        $br = '<br />';
      } else {
        $br = '';
      }
    }

    $stars_str = '';

    for ($i = 0; $i < $maxRate; $i++) {
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

      $stars_str .= '<div class="rate_star ' . $star_value . '" id="rate_star_' . $i . '"></div>';
    }

    $score_str = '';
    if ($show_score) {
      $score = ($rate_info['rate_count']) ? $rate_info['rate_count'] : 0;
      $vote_lang_var = $translate->_(array('vote', 'votes', (($rate_info['rate_count']) ? $rate_info['rate_count'] : 0)));

      $score_str = '
      <div class="item_rate_info">
        ' . $translate->_('Score:') . '  <span class="item_score">' . $item_score . ' / ' . $maxRate . ' </span> ' . $br . '
        <span class="item_votes"> ' . $score . '</span> ' . $vote_lang_var .
        '</div>';
    }

    return '<div class="he_rate_small_cont"><div class="rate_stars_cont">' . $stars_str . '</div></div><div class="clr"></div>' . $score_str;


  }
}