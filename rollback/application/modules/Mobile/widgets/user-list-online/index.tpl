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

<!--
<h3><?php echo $this->count ?> Members Online</h3>
-->

<ul class="items">
  <?php foreach( $this->paginator as $user ): ?>
	<li class='item_photo' style="padding: 3px;">
     <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', $user->getTitle()), array('title'=>$user->getTitle())) ?>
  <?php endforeach; ?>
	</li>
</ul>
<div class="clr"></div>
