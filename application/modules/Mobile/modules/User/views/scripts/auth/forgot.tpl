<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: forgot.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>&raquo;  <?php echo $this->translate('Forgot'); ?> </h4>
<div class="layout_content">
	<?php if( empty($this->sent) ): ?>

		<?php echo $this->form->render($this) ?>

	<?php else: ?>

		<div class="tip">
			<span>
				<?php echo $this->translate("USER_VIEWS_SCRIPTS_AUTH_FORGOT_DESCRIPTION") ?>
			</span>
		</div>

	<?php endif; ?>

</div>