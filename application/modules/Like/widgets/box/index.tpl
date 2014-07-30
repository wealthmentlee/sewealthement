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

		var $likesTips = new HETips($$('.like_tool_tip_links'), miniTipsOptions);
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
    <?php $this->likes = $this->all_likes; ?>
    <?php $this->period_type = 'all'; ?>
    <?php echo $this->render('_items.tpl'); ?>
  </div>

  <?php if ($this->period) : ?>
    <div class="like_list likes_month">
      <?php $this->likes = $this->month_likes; ?>
      <?php $this->period_type = 'month'; ?>
      <?php echo $this->render('_items.tpl'); ?>
    </div>
    <div class="like_list likes_week">
      <?php $this->likes = $this->week_likes; ?>
      <?php $this->period_type = 'week'; ?>
      <?php echo $this->render('_items.tpl'); ?>
    </div>
  <?php endif; ?>
</div>