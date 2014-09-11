<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: notfound.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>&raquo; <?php echo $this->translate('Error');?> </h4>

<div class="layout_content">
	<h2><?php echo $this->translate('MOBILE_Page Not Found') ?></h2>
	<p>
		<?php echo $this->translate('MOBILE_The page you have attempted to access could not be found.') ?>
	</p>

	<br />
	<a href="<?php echo $this->return_url; ?>">
		<img src='application/modules/Core/externals/images/back.png' border="0" height="12px" style="vertical-align:middle;">
		<?php echo $this->translate('MOBILE_Go to back'); ?>
	</a>
</div>