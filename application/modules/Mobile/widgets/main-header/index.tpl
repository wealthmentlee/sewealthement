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
<table class="mobile_wiget_columns_container" cellpadding="0" cellspacing="0"><tr>
	<td  valign="top">
			<?php echo $this->content()->renderWidget('mobile.menu-logo', $this->params); ?>
	</td>
	<td style="width:100%; padding-left:5px" valign="top">
			<?php echo $this->content()->renderWidget('mobile.menu-mini'); ?>
	</td>
</tr></table>