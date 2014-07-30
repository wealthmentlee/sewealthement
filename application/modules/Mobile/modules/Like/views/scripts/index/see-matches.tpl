<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: see-matches.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<div class="like-matches-container">
<?php if ($this->error): ?>
	<div class="contacts_error no_content"><?php echo $this->translate($this->message); ?></div>
<?php else: ?>

<h3><?php echo $this->translate("like_Like Matches"); ?></h3>

<span class="description"><?php echo $this->translate("like_Users who liked same things as you did."); ?></span>

<div class="like-matches">
<?php if ($this->items->getCurrentItemCount() > 0): ?>

<?php if ($this->items->getCurrentPageNumber() > 1): ?>
  <a class="pagination" href="javascript:he_list.set_page(<?php echo ($this->items->getCurrentPageNumber()-1); ?>);"><?php echo $this->translate("like_Previous"); ?></a>
<?php endif; ?>

<?php foreach ($this->items as $item): ?>
	<div class="item">
		<div class="left">
			<a href="<?php echo $item->getHref(); ?>" target="_blank">
				<span class='photo' style='background-image: url()'>
          <?php echo $this->itemPhoto($item, 'thumb.icon'); ?>
        </span>
			</a>
		</div>
		<div class="right">
			<a href="<?php echo $item->getHref(); ?>" target="_blank" title="<?php echo $item->getTitle(); ?>">
				<span class="name">
					<?php
					$display_name = $item->getTitle();
					$display_name = Engine_String::strlen($display_name) > 15 ? Engine_String::substr($display_name, 0, 15) . '...' : $display_name;
					echo $display_name;
					?>
				</span>
			</a>
			<div class="clr"></div>
			<a class="misc" href="<?php echo $this->url(array('user_id' => $item->getIdentity(), 'action' => 'index'), 'like_default' ); ?>" target="_blank">
				<?php echo $this->translate( array('%s mutual like', '%s mutual likes', $this->counts[$item->getIdentity()]), $this->counts[$item->getIdentity()] ); ?>
			</a>
		</div>
		<div class="clr"></div>
	</div>
<?php endforeach; ?>

<?php if ($this->items->count() > $this->items->getCurrentPageNumber()): ?>
  <a class="pagination" href="javascript:he_list.set_page(<?php echo ($this->items->getCurrentPageNumber()+1); ?>);"><?php echo $this->translate("like_Next"); ?></a>
<?php endif; ?>

<?php else: ?>
  <div class="no_result"><?php echo $this->translate("like_There are no users."); ?></div>
<?php endif; ?>

<div id="no_result" class="hidden"><?php echo $this->translate("like_There are no users."); ?></div>
<div class="clr"></div>

</div>

<?php endif; ?>
</div>