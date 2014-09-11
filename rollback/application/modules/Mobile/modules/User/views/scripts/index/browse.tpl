<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: browse.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>&raquo; <?php echo $this->translate('Browse Members');?></h4>

<div class='layout_middle'>
	<?php echo $this->form->render($this) ?>
		<div>
      <?php echo $this->render('_browseUsers.tpl') ?>
  </div>
</div>