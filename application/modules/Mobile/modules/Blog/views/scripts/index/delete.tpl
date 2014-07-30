<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: delete.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>&raquo;  <?php echo $this->translate('MOBILE_Confirm'); ?> </h4>

<div class="layout_content">
	<div class='global_form_popup'>
		<form method="post" class="global_form" action="<?php echo $this->url() ?>">
			<div><div>
					<h3>
						<?php echo $this->translate('Delete Blog Entry?');?>
					</h3>
					<p>
						<?php echo $this->translate('Are you sure that you want to delete the blog entry with the title "%1$s" last modified %2$s? It will not be recoverable after being deleted.', $this->blog->title,$this->timestamp($this->blog->modified_date)); ?>
					</p>
					<br />
					<p>
						<input type="hidden" name="confirm" value="true"/>
						<input type="hidden" name="return_url" value="<?php echo $this->return_url?>"/>
						<button type='submit'><?php echo $this->translate('Delete');?></button>
						<?php echo $this->translate('or');?> <a href='<?php echo urldecode($this->return_url) ?>'><?php echo $this->translate('cancel');?></a>
					</p>
			</div></div>
		</form>
	</div>
</div>