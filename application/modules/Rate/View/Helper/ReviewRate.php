<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ReviewRate.php 2010-07-02 19:47 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Rate_View_Helper_ReviewRate extends Engine_View_Helper_HtmlElement {

    public function reviewRate($score, $min = false){

        $maxRate = 5;

        $size = ($min) ? 16 : 28;
        $html = '<div style="width: 100px" class="pagereview_element">';

        for ( $i=0; $i<$maxRate; $i++ ){
            if ( $i+0.125 > $score ){
                $value = '-o';
            } else if ( $i+0.375 > $score ){
                $value = '-half-o';
            } else if ( $i+0.625 > $score ){
                $value = '-half-o';
            } else if ( $i+0.875 > $score ){
                $value = '-half-o';
            } else {
                $value = '';
            }
            $html .= '<i class="rate_style hei-star'.$value.'" id="rate_star_'.($i+1).'"></i>';
        }

        $html .= '</div>';

        return $html;

    }

}