<?php
  $instance = uniqid('');
?>

<script type="text/javascript">
en4.core.runonce.add(function(){
  var subjectLink = '<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>';
  var objectLink = '<?php echo $this->htmlLink($this->object->getHref(), $this->object->getTitle(), array("id" => "like_action_private_object_" . $instance . "_" . $this->object->getGuid() )); ?>';
  var id = "like-action-private-<?php echo $instance . "_" . $this->object->getGuid() ?>";
  if ($(id)) {
    if (en4.user.viewer.guid == en4.core.subject.guid) {
      $(id).set('html', en4.core.language.translate('like_I like %s.', objectLink));
    } else {
      $(id).set('html', en4.core.language.translate('like_%s likes %s.', subjectLink, objectLink));
    }
  }

  var options = {
			url: "<?php echo $this->url(array('action' => 'show-content'), 'like_default'); ?>",
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

		var $thumbs = $$('#<?php echo "like_action_private_object_".$instance . "_" . $this->object->getGuid(); ?>');
		var $action_hints = new HETips($thumbs, options);
});
</script>

<div class="like-action-private">
  <span class="text" id="like-action-private-<?php echo $instance . "_" . $this->object->getGuid(); ?>"></span>
</div>