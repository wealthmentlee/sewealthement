<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: WallViewMore.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Wall_View_Helper_WallViewMore extends Zend_View_Helper_HtmlElement
{
  protected $_moreLength = 255; // Note: truncation at 255 + 4 = 259 (for " ...")
  protected $_lessLength = 511;
  protected $_maxLength = 30000;
  protected $_fudgesicles = 10;
  protected $_maxLineBreaks = 4; // Truncate early if more than this nl
  protected $_tag = 'span';

  public function wallViewMore($string, $moreLength = null, $maxLength = null,
      $lessLength = null, $nl2br = true)
  {
    if( !is_numeric($moreLength) || $moreLength <= 0 ) {
      $moreLength = $this->_moreLength;
    }
    if( !is_numeric($maxLength) || $maxLength <= 0 ) {
      $maxLength = $this->_maxLength;
    }
    if( !is_numeric($lessLength) || $lessLength <= 0 ) {
      $lessLength = $this->_lessLength;
    }

    // If using line breaks, ensure that there are not too many line breaks
/*    if( $nl2br ) {
      $string = trim(preg_replace('/[\r\n]+/', "\n", $string));
      if( ($c = substr_count($string, "\n")) > $this->_maxLineBreaks) {
        $pos = 0;
        for( $i = 0; $i < $this->_maxLineBreaks; $i++ ) {
          $pos = strpos($string, "\n", $pos + 1);
        }
        if( $pos <= 0 || !is_int($pos) ) {
          $pos = null;
        }
        if( $pos ) {
          $moreLength = $pos;
        }
      }
    }*/

    // If length is less than max len, just return
    $strLen = Engine_String::strlen($string);
    if( $strLen <= $moreLength + $this->_fudgesicles ) {
      if( $nl2br ) {
        return nl2br($string);
      } else {
        return $string;
      }
    }

    // Otherwise truncate
    if( $strLen >= $maxLength ) {
      $strLen = $maxLength;
      $string = $this->htmlSubstr($string, $maxLength) . $this->view->translate('... &nbsp;');
    }

    $shortText = $this->htmlSubstr($string, $moreLength);
    $fullText = $string;

    // Do nl2br
    if( $nl2br ) {
      $shortText = nl2br($shortText);
      $fullText = nl2br($fullText);
    }

    $content = '<'
      . $this->_tag
      . ' class="view_more"'
      . '>'
      . $shortText
      . $this->view->translate('... &nbsp;')
      . '<a class="view_more_link" href="javascript:void(0);" onclick="$(this).getParent().getNext().style.display=\'\';$(this).getParent().style.display=\'none\';">'.$this->view->translate('more').'</a>'
      . '</'
      . $this->_tag
      . '>'
      . '<'
      . $this->_tag
      . ' class="view_more"'
      . ' style="display:none;"'
      . '>'
      . $fullText
      . ' &nbsp;'
      ;

    if( $strLen >= $lessLength ) {
      $content .= '<a class="view_less_link" href="javascript:void(0);" onclick="$(this).getParent().getPrevious().style.display=\'\';$(this).getParent().style.display=\'none\';">'.$this->view->translate('less').'</a>';
    }

    $content .= '</'
      . $this->_tag
      . '>'
      ;

    return $content;
  }

  public function setMoreLength($length)
  {
    if( is_numeric($length) && $length > 0 )
    {
      $this->_moreLength = $length;
    }

    return $this;
  }

  public function setMaxLength($length)
  {
    if( is_numeric($length) && $length > 0 )
    {
      $this->_maxLength = $length;
    }

    return $this;
  }

  public function htmlSubstr($html, $length)
  {
    $out = '';
    $arr = preg_split('/(<.+?>|&#?\\w+;)/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    $tagStack = array();

    for ($i = 0, $l = 0; $i < count($arr); $i++) {
      if ($i & 1) {
        if (substr($arr[$i], 0, 2) == '</' or substr($arr[$i], 0, 2) == '[/') {
          array_pop($tagStack);
        } elseif ($arr[$i][0] == '&') {
          $l++;
        } elseif (substr($arr[$i], -2) != '/>' or substr($arr[$i], -2) != '/]') {
          array_push($tagStack, $arr[$i]);
        }

        $out .= $arr[$i];
      } elseif (substr($arr[$i], -2) != '/>') {
        if (($l += strlen($arr[$i])) >= $length) {
          $out .= substr($arr[$i], 0, $length - $l + strlen($arr[$i]));
          break;
        } else {
          $out .= $arr[$i];
        }
      }
    }

    while (($tag = array_pop($tagStack)) !== NULL) {
      $out .= '</' . strtok(substr($tag, 1), " \t>") . '>';
    }

    return $out;
  }

}