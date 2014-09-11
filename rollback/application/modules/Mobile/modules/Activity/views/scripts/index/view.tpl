<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>&raquo;  <?php echo $this->translate('Activity'); ?> </h4>

<div class="layout_content">
	<ul class='items subcontent'>
		<?php echo $this->mobileActivity($this->action, array(
			'action_id' => $this->action->action_id,
			'viewAllComments' => true,
			'viewAllLikes' => true,
		), 'comment') ?>
	</ul>
</div>
