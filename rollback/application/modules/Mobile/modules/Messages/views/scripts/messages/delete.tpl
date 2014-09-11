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
		<?php if ($this->success): ?>

			<div class="global_form_popup_message">
				<?php echo $this->translate('The selected messages have been deleted.') ?>
			</div>

		<?php else: // success == false ?>

		<form method="POST" action="<?php echo $this->url() ?>" class="global_form">
			<div><div>
				<h3>
					<?php echo $this->translate('Delete Message(s)?') ?>
				</h3>
				<p>
					<?php echo $this->translate('Are you sure that you want to delete the selected message(s)? This action cannot be undone.') ?>
				</p>

				<p>&nbsp;</p>

				<p>
					<input type="hidden" name="message_id" value="<?php echo $this->message_id?>"/>
					<input type="hidden" name="return_url" value="<?php echo $this->return_url?>"/>
					<button type='submit'><?php echo $this->translate('Delete') ?></button>
					<?php echo $this->translate('or') ?>
					<a href="<?php echo urldecode($this->return_url)?>"><?php echo $this->translate('cancel') ?></a>
				</p>
			</div></div>
		</form>
		<?php endif; // success ?>
</div>