<div class="like_hint">
	<div class="like_hint_body">
		<div class="like_hint_right">
			<?php echo $this->htmlLink($this->user->getHref(), $this->itemPhoto($this->user, 'thumb.profile', '', array('width' => '100px', 'height' => '100px', 'target' => '_blank'))); ?>
		</div>
		<div class="like_hint_left">
			<div class="title">
				<?php
					$display_name = $this->user->getTitle();
					echo $this->htmlLink($this->user->getHref(), $display_name, array('target' => '_blank'));
				?>
			</div>
			<div class="clr"></div>
			<?php if ($this->paginator->getTotalItemCount() > 0): ?>
			<div class="horizontal_list">
				<?php if (!$this->isSelf): ?>
					<a class="item_count" href="javascript:void(0)" onclick="like.get_mutual_friends(<?php echo (int)$this->user->getIdentity(); ?>);">
						<?php echo $this->translate(array('like_%s mutual friend', 'like_%s mutual friends', $this->paginator->getTotalItemCount()), ($this->paginator->getTotalItemCount())); ?>
					</a>
				<?php else: ?>
					<a class="item_count" href="javascript:void(0)" onclick="like.get_friends(<?php echo (int)$this->user->getIdentity(); ?>);">
						<?php echo $this->translate(array('like_%s friend', 'like_%s friends', $this->paginator->getTotalItemCount()), ($this->paginator->getTotalItemCount())); ?>
					</a>
				<?php endif; ?>
				<?php foreach($this->paginator as $item): ?>
					<div class="item">
						<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon', '', array('width' => '32px', 'height' => '32px')), array('class' => 'like_hint_tip_links')); ?>
						<div class="like_hint_title hidden"></div>
						<div class="like_hint_text hidden"><?php echo $item->getTitle(); ?></div>
					</div>
				<?php endforeach; ?>
				<div class="clr"></div>
			</div>
			<div class="clr"></div>
			<?php endif; ?>
		</div>
		<div class="clr"></div>
	</div>
	<div class="like_hint_options">
		<?php if (!$this->isSelf): ?>
			<?php echo $this->htmlLink($this->url(array('action' => 'compose', 'to' => $this->user->getIdentity()), 'messages_general'), $this->translate("Send Message"), array('class' => 'like_hint_send_message', 'target' => '_blank')); ?>
			<?php echo $this->userFriendship($this->user); ?>
			<div class="clr"></div>
		<?php endif; ?>
	</div>
</div>