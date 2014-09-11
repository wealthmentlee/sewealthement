<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _composeLikeAction.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<script type="text/javascript">
if (window.en4) {
  en4.core.runonce.add(function(){
    var guid = "<?php echo $this->object->getGuid(); ?>";
    var nodeId = 'like_action_'+guid;
    var internalTips = null;
    var miniTipsOptions = {
      'url' : '<?php echo $this->url( array("action" => "show-matches"), "like_default" ); ?>',
      'delay' : 300,
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

    var $actionLikesTips = new HETips($$("<?php echo '.like_action_tip_'.$this->object->getGuid(); ?>"), miniTipsOptions);
    if (!window.likeActions) {
      window.likeActions = [];
    }
    window.likeActions.push(new LikeAction(nodeId, {
      userIds : <?php echo Zend_Json::encode($this->user_ids); ?>,
      likeCount : <?php echo (int)$this->likeCount; ?>
    }));

  });
}
</script>

<div class="like_action_container">
	<div class="left">
		<?php echo $this->htmlLink($this->object->getHref(), $this->itemPhoto($this->object, 'thumb.profile', '', array('height' => '100px'))); ?>
	</div>
	<div class="right">
		<div class="top">
			<?php echo $this->htmlLink($this->object->getHref(), $this->object->getTitle()); ?>
		</div>
		<div class="desc">
			<?php
				$descr = $this->object->getDescription();
				$descr = Engine_String::strlen($descr) > 100 ? Engine_String::substr($descr, 0, 100) . '...' : $descr;
				echo $descr;
			?>
		</div>
		<div class="count" id="like_action_<?php echo $this->object->getGuid(); ?>"></div>
		<?php $friendCount = count($this->users); ?>
		<?php if ($friendCount > 0): ?>
			<div class="likes">
				<?php foreach($this->users as $user): ?>
				<div class="item">
					<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon', '', array('width' => '100px', 'height' => '100px')), array('class' => 'like_action_tip_'.$this->object->getGuid(), 'id' => $this->object->getGuid().'_like_action_item_'.$user->getGuid())); ?>
				</div>
				<?php endforeach; ?>
				<div class="clr"></div>
			</div>
		<?php endif; ?>
	</div>
	<div class="clr"></div>
</div>