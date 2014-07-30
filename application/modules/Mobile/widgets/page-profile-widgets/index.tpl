<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

?>
<?php
	$left = $this->left;
	$right = $this->right;
?>
<div class="profile-left">
	<?php foreach($left as $widget): ?>
		<?php echo $this->content()->renderWidget($widget); ?>
	<?php endforeach; ?>
</div>

<div class="profile-right">
	<?php foreach($right as $widget): ?>
		<?php echo $this->content()->renderWidget($widget); ?>
	<?php endforeach ?>
</div>
<div class="clr"></div>