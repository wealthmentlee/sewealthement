<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: success.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<div class="success">
  <?php foreach( $this->messages as $message ): // Show messages ?>
    <div class='messages'>
      <?php echo $message ?>
    </div>
  <?php endforeach; ?>
	<div class='button'>
			<a href="<?php echo $this->return_url; ?>">
				<img src='application/modules/Core/externals/images/back.png' border="0" height="12px" style="vertical-align:middle;">
				<?php echo $this->translate('MOBILE_Go to back'); ?>
			</a>
	</div>
</div>