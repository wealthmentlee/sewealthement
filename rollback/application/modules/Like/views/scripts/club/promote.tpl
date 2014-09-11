<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: promote.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->error): ?>
<div class="page_error"><?php echo $this->message; ?></div>
<?php else: ?>

<div class="promote_like_box">

	<h2><?php echo $this->translate("like_LIKE_PROMOTE_BOX_TITLE"); ?></h2>
	<?php echo $this->translate("like_LIKE_PROMOTE_BOX_DESC"); ?>
	<br />
	<div class="promote_left">
	  <div class="like_desc"><?php echo $this->translate("like_Like Button Code."); ?></div>
		<div class="like_textarea">
			<textarea onfocus="this.select();" cols="10" rows="7" name="like_button_snippet" id="like_button_snippet"><iframe scrolling="no" frameborder="0" style="background:transparent;border:none;overflow:hidden;width:400px;height:40px;" allowTransparency="true" src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->url(array('object' => $this->object, 'object_id' => $this->object_id), 'like_button'); ?>" id="like_button_frame" ></iframe></textarea>
		</div>
	</div>
	
	<div class="promote_right">
		<div class="like_desc"><?php echo $this->translate("like_Like Button Preview:"); ?> </div>
		<iframe scrolling="no" frameborder="0" style="background:transparent;border:none;overflow:hidden;width:400px;height:40px;" allowTransparency="true" src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->url(array('object' => $this->object, 'object_id' => $this->object_id), 'like_button'); ?>" id="like_button_frame" ></iframe>
	</div>
	
	<div style="clear: both;"></div>

	<div class="promote_left">
		<div class="like_desc"><?php echo $this->translate("like_Like Box Code."); ?></div>
		<div class="like_textarea">
			<textarea onfocus="this.select();" cols="10" rows="7" name="like_box_snippet" id="like_box_snippet"><iframe scrolling="no" frameborder="0" style="background:transparent;border:none;overflow:hidden;width:255px;height:310px;" allowTransparency="true" src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->url(array('action' => 'show-like-box', 'object' => $this->object, 'object_id' => $this->object_id), 'show_like_box'); ?>" id="like_box_frame" ></iframe></textarea>
		</div>
	</div>
	
	<div class="promote_right">
		<div class="like_desc"><?php echo $this->translate("like_Like Box Preview:"); ?> </div>
    <iframe scrolling="no" frameborder="0" style="background:transparent;border:none;overflow:hidden;width:255px;height:310px;" allowTransparency="true" src="<?php echo 'http://'.$_SERVER['HTTP_HOST'].$this->url(array('action' => 'show-like-box', 'object' => $this->object, 'object_id' => $this->object_id), 'show_like_box'); ?>" id="like_box_frame" ></iframe>
	</div>

	<div style="clear: both;"></div>
	
</div>

<?php endif; ?>
