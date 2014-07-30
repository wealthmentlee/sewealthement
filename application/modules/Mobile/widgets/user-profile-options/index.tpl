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

<div class="mobile_widget_container">
	<div id='mobile_profile_options'>
		<?php // This is rendered by application/modules/core/views/scripts/_navIcons.tpl
			echo $this->navigation()
				->menu()
				->setContainer($this->navigation)
				->setPartial(array('_mobileNavIcons.tpl', 'mobile'))
				->render()
		?>
	</div>
</div>