<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _composeItems.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<?php if ( count($this->items) > 0): ?>
 <div id="all_list_pages" class="list_type">
   <?php
		$counter = 0;
		$nophoto_items = array('blog', 'pageblog');
	 ?>
   <?php foreach ($this->items as $type => $data): ?>
	 <h4 class="module_type_header module_<?php echo $type; ?>" style="background-image: url(<?php echo $this->baseUrl().$this->icons[$type]; ?>);"><?php echo $this->translate($this->labels[$type]); ?></h4>
	 <div class="module_type_wrapper">
		<?php foreach ($data as $item): ?>
     <div href="<?php echo $item->getHref(); ?>" target="_blank" class="item" id="<?php echo $item->getGuid(); ?>">
       <span class='photo like_tool_tip_smoothbox' style='background-image: url();'>
				 <?php
					if ( in_array( $item->getType(), $nophoto_items ) ){
						$photo = $this->htmlImage($this->baseUrl() . '/application/modules/Like/externals/images/nophoto/' . $item->getType() . '.png', '', array(
							'class' => 'thumb_icon item_photo_' . $item->getType()
						));
					}else{
						$photo = $this->itemPhoto($item, 'thumb.icon');
					}
				?>
				 <?php echo $this->htmlLink($item->getHref(), $photo, array('target' => '_blank')); ?>
			 </span>
       <span class="name" title="<?php echo $item->getTitle(); ?>" >
				<?php
					$display_name = $item->getTitle();
					$display_name = Engine_String::strlen($display_name) > 10 ? Engine_String::substr($display_name, 0, 10) . '...' : $display_name;
					echo $this->htmlLink($item->getHref(), $display_name, array('target' => '_blank'));
				?>
			 </span>

      <?php if (Engine_Api::_()->like()->isLike($item)): ?>
       <div style="display: none;" class="like_button_container"><a href="javascript:void(0)" class="like_button_link like" id="like_<?php echo $item->getGuid(); ?>"><span class="like_button"><?php echo $this->translate("like_Like"); ?></span></a></div>
       <div class="like_button_container"><a href="javascript:void(0)" class="like_button_link unlike" id="unlike_<?php echo $item->getGuid(); ?>"><span class="unlike_button"><?php echo $this->translate("like_Unlike"); ?></span></a></div>
      <?php else: ?>
       <div class="like_button_container"><a href="javascript:void(0)" class="like_button_link like" id="like_<?php echo $item->getGuid(); ?>"><span class="like_button"><?php echo $this->translate("like_Like"); ?></span></a></div>
       <div style="display: none;" class="like_button_container"><a href="javascript:void(0)" class="like_button_link unlike" id="unlike_<?php echo $item->getGuid(); ?>"><span class="unlike_button"><?php echo $this->translate("like_Unlike"); ?></span></a></div>
      <?php endif; ?>

       <div class="clr"></div>
     </div>
     <?php
      $counter++;
      if ($counter % 3 == 0){
        echo '<div class="clr"></div>';
      }
     ?>
		<?php endforeach; ?>
		<div class="clr"></div>
		</div>
	 <?php endforeach; ?>
   <div class="clr"></div>
 </div>
 <div class="clr"></div>
 <div class="like_see_all_likes">
	 <?php echo $this->htmlLink($this->url(array('action' => 'index', 'period_type' => 'all'), 'like_default'), $this->translate('like_See All Likes'), array('target' => '_blank')); ?>
 </div>
<?php endif; ?>

<div class="clr" id="he_contacts_end_line"></div>
<div id="he_disabled_div" class="hidden"></div>