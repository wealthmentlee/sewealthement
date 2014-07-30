<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: friends.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<h4>
  &raquo; <?php echo $this->translate('Friends');?>
  <?php if ($this->subject && $this->subject->getIdentity()):?>&raquo; <?php echo $this->subject->__toString()?><?php endif;?>
</h4>

<div class="layout_content">
	<ul class='items'>

		<?php foreach( $this->friends as $membership ):
			if( !isset($this->friendUsers[$membership->user_id]) ) continue;
			$member = $this->friendUsers[$membership->user_id];
			?>

			<li>
				<div class="item_photo">
					<?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'profile_friends_icon')) ?>
				</div>

				<div class='item_body'>
					<div class='item_options' style="padding-right:10px;">
						<?php echo $this->mobileUserFriendship($member) ?>
					</div>

					<div class='item_title'>
						<?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?>
					</div>
				</div>

			</li>

		<?php endforeach;?>
	</ul>

	<?php if($this->friends->count() > 1):?>
		<?php echo $this->paginationControl($this->friends, null, array('pagination/search.tpl', 'mobile')); ?>
	<?php endif;?>
</div>