<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _composeLikeBox.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<?php
  $background = "background: url(" . $this->base_url . $this->baseUrl() . "/application/modules/Like/externals/images/like_button.png) no-repeat left 0px;";
?>
 <style type="text/css">
  .like_box_container{
     background: none repeat scroll 0 0 white;
     border-color: #AAAAAA #AAAAAA #AAAAAA;
     border-radius: 0 0 0 0;
     border-style: solid;
     border-width: 1px;
     font-family: tahoma,arial,verdana,sans-serif;
     overflow: hidden;
     text-decoration: none;
     width: 240px;
  }
  .like_box_container .like_box_header{
      background-color: #EDEFF4;
      border-bottom: 1px solid #C6CEDD;
      color: #1C2A47;
      cursor: default;
      direction: ltr;
      font-size: 13.3333px;
      font-style: normal;
      font-weight: bold;
      letter-spacing: normal;
      line-height: 16px;
      padding: 8px 10px 7px;
      text-align: left;
      text-transform: none;
      vertical-align: baseline;
      word-spacing: normal;
  }
  .like_box_container .like_box_header .header_text{
    display: block;
    float: left;
    font-family: tahoma,arial,verdana,sans-serif;
  }
  [dir="rtl"] .like_box_container .like_box_header .header_text{
    float: right;
  }
  .like_box_container .like_box_header .header_image{
    float: right;
    display: block;
  }
  .like_box_container .like_box_content{
    padding: 5px;
  }
  .like_box_container .like_box_content .like_box_info{
    border-bottom: 1px solid #D8DFEA;
    margin: 5px;
  }
  .like_box_container .like_box_content .like_box_info .like_box_left{
    float: left;
    padding: 5px;
  }
  [dir="rtl"] .like_box_container .like_box_content .like_box_info .like_box_left{
    float: right;
  }
  .like_box_container .like_box_content .like_box_info .like_box_left .like_box_photo a{
    font-weight: bold;
    text-decoration: none;
  }
  .like_box_container .like_box_content .like_box_info .like_box_right{
    float: left;
    margin-top: 5px;
    padding: 5px;
  }
  [dir="rtl"] .like_box_container .like_box_content .like_box_info .like_box_right{
    float: right;
  }
  .like_box_container .like_box_content .like_box_info .like_box_right .like_box_details a{
    font-family: tahoma,arial,verdana,sans-serif;
    font-size: 14px;
    font-weight: bold;
    text-decoration: none;
  }
  .like_box_container .like_box_content .like_box_info .like_box_right .like_button_container{
    -moz-box-shadow: 0 0 1px 0 #888888;
    -webkit-box-shadow: #888 0px 0px 1px 0px;
    float: left;
    position: relative;
  }
  [dir="rtl"] .like_box_container .like_box_content .like_box_info .like_box_right .like_button_container{
    float: right;
  }
  .like_box_container .like_box_content .like_box_info .like_box_right .like_button_container a{
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
  .like_box_desc{
    font-family: tahoma,arial,verdana,sans-serif;
    font-size: 11px;
    padding: 5px 0 4px 5px;
  }
  .like_box_likes{
    padding: 5px;
  }
  .like_box_likes .like_box_like{
    float: left;
    padding: 2px;
    width: 50px;
  }
  [dir="rtl"] .like_box_likes .like_box_like{
    float: right;
  }
 </style>
<div class="like_box_container">
	<div class="like_box_header">
	  <span class="header_text">
      <?php echo $this->translate("like_Find us on"); ?> <?php echo $this->htmlLink($this->base_url . $this->baseUrl(), $this->layout()->siteinfo['title'], array('target'=>'_blank','style'=>'text-decoration:none;')); ?>
    </span>
	  <?php if ($this->icon_url): ?>
	  <span class="header_image"">
	    <?php echo $this->htmlImage($this->icon_url); ?>
	  </span>
	  <?php endif; ?>
	  <div style="clear: both;"></div>
	</div>
	<div class="like_box_content">
		<div class="like_box_info">
			<div class="like_box_left">
				<div class="like_box_photo">
					<a target="_blank" href="<?php echo $this->subject->getHref(); ?>">
					<?php
					  $class = "thumb_icon item_photo_like_club";
          if (!$this->subject->getPhotoUrl('thumb.icon')){
            $class .= " item_nophoto";
						$photo_url = $this->baseUrl()."/application/modules/Like/externals/images/nophoto_like_thumb_icon.png";
					}else{
						$photo_url = $this->subject->getPhotoUrl('thumb.icon');
					}
					?>
						<img border="0" width='48px' height='48px' style="border:1px solid #DDDDDD;" class="<?php echo $class; ?>" src="<?php echo $photo_url; ?>" />
					</a>
				</div>
			</div>
			<div class="like_box_right">
				<div class="like_box_details"><?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle(), array('class' => 'bold', 'target' => '_blank', 'style' => 'text-decoration:none;font-weight: bold; font-family: tahoma,arial,verdana,sans-serif; font-size: 14px;')); ?></div>
				<div style="clear: both;"></div>
				
				<div class="like_button_container">
					<a id="_<?php echo $this->subject->getGuid(); ?>" style="background:transparent url(<?php echo $this->baseUrl(); ?>/application/modules/Like/externals/images/like_button_bg.png) repeat scroll 0 0;" class="like_button_link <?php echo $this->actionName; ?>" href="<?php echo $this->subject->getHref(); ?>" target="_blank">
						<span class="like_button" style="<?php echo (isset($background)) ? $background : ''; ?> padding-left: 16px; font-weight: bold; text-decoration: none; font-family: 'lucida grande',tahoma,verdana,arial,sans-serif; font-size: 11px; color: #748AB7;" >
							<?php echo $this->translate('like_Like'); ?>
						</span>
					</a>
				</div>
				
				<div style="clear: both;"></div>
			</div>
			<div style="clear: both;"></div>
		</div>
		<?php if ($this->likes->getTotalItemCount() > 0): ?>
			<div class="like_box_desc"><?php echo $this->translate(array("like_%s person like it.", "like_%s people like it.", $this->likes->getTotalItemCount()), ($this->likes->getTotalItemCount())); ?></div>
			<div class="like_box_likes">
				<?php if ($this->likes->getCurrentItemCount() > 0): ?>
					<?php foreach ($this->likes as $like): ?>
						<div class="like_box_like">
							<div class="l"><?php echo $this->htmlLink($like->getHref(), $this->likePhoto($like, 'thumb.icon', '', array('width' => '48px', 'height' => '48px', 'style' => 'text-decoration:none;border:1px solid #DDDDDD;')), array('target' => '_blank', 'border' => 0));  ?></div>
							<div class="r"><?php echo $this->htmlLink($like->getHref(), $like->getTitle(), array('target' => '_blank', 'style' => 'text-decoration:none;color: #808080; font-size: 9px; padding-top: 2px; font-family: tahoma,arial,verdana,sans-serif; text-align: center; white-space: normal; display: block; width: 50px; height: 22px; line-height:1;')); ?></div>
							<div style="clear: both;"></div>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="like_box_no_result like_box_desc" style="padding: 5px 0 4px 5px;	font-size: 11px;"><?php echo $this->translate("like_There is no likes."); ?></div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
	<div style="clear: both;"></div>
</div>