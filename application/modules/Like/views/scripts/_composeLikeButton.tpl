<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _composeLikeButton.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<?php $baseUrl = $this->base_url . $this->baseUrl(); ?>

<?php
 if ($this->actionName == 'like'){
  $background = "background: url(" . $baseUrl . "/application/modules/Like/externals/images/like_button.png) no-repeat left 0px;";
 }else{
  $background = "background: url(" . $baseUrl . "/application/modules/Like/externals/images/unlike_button.png) no-repeat left 0px;";  
 }
?>
<style type="text/css">
  .like_button_button{
    float: left;
  }
  [dir="rtl"] .like_button_button{
    float: right;
  }
  .like_button_button .like_button_container{
    -moz-box-shadow: 0 0 1px 0 #888;
    -webkit-box-shadow: #888 0px 0px 1px 0px;
    float: left;
    position: relative;
  }
  .like_button_button .like_button_container a{
    border-color: #999999 #999999 #888888;
    border-style: solid;
    border-width: 1px;
    color: #333333;
    cursor: pointer;
    display: block;
    float: left;
    padding: 4px 5px;
    text-decoration: none;
  }
  .like_button,
  .unlike_button{
    color: #748AB7;
    font-family: 'lucida grande',tahoma,verdana,arial,sans-serif;
    font-size: 11px;
    font-weight: bold;
    padding-left: 16px;
    text-decoration: none;
  }
  .like_button_likes{
    float: left;
    margin-left: 5px;
    font-family: 'lucida grande',tahoma,verdana,arial,sans-serif;
    font-size: 11px; width: 80%;
  }
  [dir="rtl"] .like_button_likes{
    float: right;
    margin-right: 5px;
  }
</style>
<div class="like_button_wrapper">
  
  <div id="like_button_wrapper" class="like_button_button">
    <div class="like_button_container">
      <a id="_<?php echo $this->subject->getGuid(); ?>" style="background:transparent url(<?php echo $baseUrl; ?>/application/modules/Like/externals/images/like_button_bg.png) repeat scroll 0 0;" class="like_button_link <?php echo $this->actionName; ?>" href="javascript:void(0)">
        <span class="<?php echo $this->actionName; ?>_button" style="<?php echo $background; ?>" >
          <?php echo $this->translate('like_'.ucfirst($this->actionName)); ?>
        </span>
      </a>
    </div>
    <div class="clr" style="clear: both;"></div>
  </div>
  
  <table style="width: 80%; height: 50%;" cellpadding="0" cellspacing="0">
    <tr>
      <?php if ($this->icon_url): ?>
      <td valign="top" width="16">
        <img style="margin: 3px; 5px 3px 3px" src="<?php echo $this->icon_url; ?>" />
      </td>
      <?php endif; ?>
      <td valign="middle">
        <div id="like_button_likes" class="like_button_likes">
          <?php echo $this->displayLikes($this->likes, $this->like_count, true, $this->is_liked); ?>
          <div class="clr" style="clear: both;"></div>
        </div>
      </td>
    </tr>
  </table>
  
  <div class="clr" style="clear: both;"></div>
  
</div>