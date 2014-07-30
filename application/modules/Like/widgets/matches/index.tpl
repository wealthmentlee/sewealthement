<script type="text/javascript">
	var internalTips = null;
	en4.core.runonce.add(function(){
		var options = {
			url: '<?php echo $this->url( array("action" => "show-matches"), "like_default" ); ?>',
			delay: 300,
			onShow: function(tip, element){
				var miniTipsOptions = {
					'htmlElement': '.he-hint-text',
					'delay': 1,
					'className': 'he-tip-mini',
					'id': 'he-mini-tool-tip-id',
					'ajax': false,
					'visibleOnHover': false
				};

				internalTips = new HETips($$('.he-hint-tip-links'), miniTipsOptions);
				Smoothbox.bind();
			}
		};

		var $thumbs = $$('.like_match_item');
		var $matches_tips = new HETips($thumbs, options);
	});
</script>

<div class="like_matches">
	<div class="see_all_container">
		<a href="<?php echo $this->url( array('action' => 'see-matches', 'user_id' => $this->subject->getIdentity()), 'like_default'); ?>" class="smoothbox">
			<?php echo $this->translate(array("like_%s user", "like_%s users", $this->items->getTotalItemCount()), ($this->items->getTotalItemCount())); ?>
		</a>
	</div>
	<div class="clr"></div>
	<?php 
	$counter = 0;
	foreach ($this->items as $item):
	$counter++;
	?>
		<div class="item">
			<div class="photo">
				<?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'like_match_item', 'id' => 'like_match_item_'.$item->getGuid())); ?>
			</div>
		</div>
		<?php if ($counter % 3 == 0): ?>
			<div class="clr"></div>
		<?php endif; ?>
	<?php endforeach; ?>
	<div class="clr"></div>
</div>