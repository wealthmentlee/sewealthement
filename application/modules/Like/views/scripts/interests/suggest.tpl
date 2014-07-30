<li class="autosuggest-choice" id="<?php echo $this->item->getIdentity(); ?>" value="0">
	<div class="suggest-wrapper">
		<div class="suggest-photo">
			<?php echo $this->itemPhoto($this->item, 'thumb.icon', '', array('width' => '32px', 'height' => '32px')); ?>
		</div>
		<div class="suggest-title">
			<span><?php echo $this->item->getTitle(); ?></span>
		</div>
		<div class="clr"></div>
	</div>
</li>