<?php echo $this->fieldValueLoop($this->subject(), $this->fieldStructure) ?>

<?php if (!empty($this->items) && $this->showInterests && $this->subject->getType() == 'user' ) { ?>

<script type="text/javascript">
	var internalTips = null;
	en4.core.runonce.add(function(){
		var options = {
			url: "<?php echo $this->url(array('action' => 'show-content'), 'like_default'); ?>",
			delay: 300,
      onShow: function(tip, element) {
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

		var $thumbs = $$('.like-interest-items');
		var $mosts_hints = new HETips($thumbs, options);
	});
</script>

<div class="profile_fields">
	<h4><span><?php echo $this->translate("like_Interests"); ?></span></h4>
	<?php foreach($this->labels as $type => $label): ?>
		<?php
			$checkModule = '';
			switch ($type){
				case 'music_playlist' : {
					$checkModule = 'music';
				}
				break;
				default : {
					$checkModule = $type;
				}
				break;
			}
			if (!$this->moduleApi->isModuleEnabled($checkModule)){
				continue;
			}

	    $html = array();
      if (isset($this->items[$type])) {
        foreach($this->items[$type] as $item) {
            $html[] = $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'like-interest-items', 'id' => 'like-interest-item_'.$item->getGuid()));
        }
      }
      if(isset($this->fake_items[$type])){
        foreach($this->fake_items[$type] as $fake_item){
           $html[] = '<label>'.$fake_item['resource_title'].'</label>';
        }
      }
      shuffle($html);
     ?>

		<?php if ($html): ?>
			<ul>
				<li><span><?php echo $this->translate($label); ?></span><span><?php echo implode(', ', $html); ?></span></li>
			</ul>
		<?php endif; ?>
    
	<?php endforeach; ?>
</div>

<?php } ?>

<?php if ($this->isSelf): ?>
	<div class="profile_edit_interests">
		<?php echo $this->htmlLink($this->url(array(), 'like_interests'), $this->translate('like_Edit Interests')); ?>
	</div>
<?php endif; ?>