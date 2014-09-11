<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: login.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>&raquo;  <?php echo $this->translate('MOBILE_Confirm'); ?> </h4>
<div class="layout_content">
	<?php if( empty($this->reset) ): ?>

		<?php echo $this->form->render($this) ?>

	<?php else: ?>

		<div class="tip">
			<span>
				<?php echo $this->translate("Your password has been reset. Click %s to sign-in.", $this->htmlLink(array('route' => 'user_login'), $this->translate('here'))) ?>
			</span>
		</div>

	<?php endif; ?>

</div>