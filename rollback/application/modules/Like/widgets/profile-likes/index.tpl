<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<script type="text/javascript">
	en4.core.runonce.add(function(){

		var miniTipsOptions = {
			'htmlElement': '.like_tip_text',
			'delay': 1,
			'className': 'he-tip-mini',
			'id': 'he-mini-tool-tip-id',
			'ajax': false,
			'visibleOnHover': false
		};

		var $profileLikesTips = new HETips($$('.like_profile_tip'), miniTipsOptions);
	});
</script>

<div class="he_like_cont">
  <?php if ($this->period) : ?>
  <ul class="like_list_switcher">
    <li><a href="javascript://" onclick="showLikesList(this, 'all');" class="active"><?php echo $this->translate('LIKE_Overall'); ?></a></li>
    <li><a href="javascript://" onclick="showLikesList(this, 'month');"><?php echo $this->translate('LIKE_This Month'); ?></a></li>
    <li><a href="javascript://" onclick="showLikesList(this, 'week');"><?php echo $this->translate('LIKE_This Week'); ?></a></li>
  </ul>
  <div class="clr"></div>
  <?php endif; ?>

  <div class="like_list likes_all active_list">
    <?php $this->items = $this->items_all; ?>
    <?php $this->total = $this->total_all; ?>
    <?php $this->period_type = 'all'; ?>
    <?php $this->likedMembersAndPages = $this->likedMembersAndPages_all; ?>
    <?php echo $this->render('_items.tpl'); ?>
  </div>

  <?php if ($this->period) : ?>
    <div class="like_list likes_month">
      <?php $this->items = $this->items_month; ?>
      <?php $this->total = $this->total_month; ?>
      <?php $this->period_type = 'month'; ?>
      <?php $this->likedMembersAndPages = $this->likedMembersAndPages_month; ?>
      <?php echo $this->render('_items.tpl'); ?>
    </div>
    <div class="like_list likes_week">
      <?php $this->items = $this->items_week; ?>
      <?php $this->total = $this->total_week; ?>
      <?php $this->period_type = 'week'; ?>
      <?php $this->likedMembersAndPages = $this->likedMembersAndPages_week; ?>
      <?php echo $this->render('_items.tpl'); ?>
    </div>
  <?php endif; ?>

</div>