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

<h4>&raquo; <?php echo $this->translate('MOBILE_Confirm'); ?> </h4>
	<div class="layout_content">
		<form method="POST" action="<?php echo $this->url() ?>" class="global_form_popup">
			<div><div>
				<h3>
					<?php if (!empty($this->comment_id)): ?>
					<?php echo $this->translate("Delete Comment?") ?>
					<?php else: ?>
					<?php echo $this->translate("Delete Activity Item?") ?>
					<?php endif; ?>
				</h3>
				<p>
					<?php if (!empty($this->comment_id)): ?>
					<?php echo $this->translate("Are you sure that you want to delete this comment? This action cannot be undone.") ?>
					<?php else: ?>
					<?php echo $this->translate("Are you sure that you want to delete this activity item and all of its comments? This action cannot be undone.") ?>
					<?php endif; ?>

				</p>

				<p>&nbsp;</p>

				<p>
					<input type="hidden" name="action_id" value="<?php echo $this->action_id?>"/>
					<input type="hidden" name="return_url" value="<?php echo $this->return_url?>"/>
					<?php if (!empty($this->comment_id)): ?>
					<input type="hidden" name="comment_id" value="<?php echo $this->comment_id?>"/>
					<?php endif; ?>
					<button type='submit'><?php echo $this->translate("Delete") ?></button>
					<?php echo $this->translate(" or ") ?>
					<a href="<?php echo urldecode($this->return_url); ?>"><?php echo $this->translate("cancel") ?></a>
				</p>
			</div></div>
		</form>
	</div>
</div>