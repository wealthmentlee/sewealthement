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

<ul class='items'>

  <?php foreach ($this->likes as $like): ?>

    <li>
			<div class="item_photo">
      	<?php echo $this->htmlLink($like->getHref(), $this->itemPhoto($like, 'thumb.icon'), array('class' => 'profile_friends_icon')) ?>
			</div>

      <div class='item_body'>
				<div class='item_options'>
        	<?php echo $this->mobileUserFriendship($like); ?>
      	</div>

        <div class='item_title'>
          <?php echo $this->htmlLink($like->getHref(), $like->getTitle()); ?>
        </div>
      </div>

    </li>

  <?php endforeach;?>
</ul>