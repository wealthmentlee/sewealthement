<script type="text/javascript">
  var internalTips = null;
  en4.core.runonce.add(function(){
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
    var $thumbs = $$('.most-liked-album');
    var $mosts_hints = new HETips($thumbs, options);
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
    <?php echo $this->render('_items_page.tpl'); ?>
  </div>

  <?php if ($this->period) : ?>
  <div class="like_list likes_month">
    <?php $this->likes = $this->month_likes; ?>
    <?php echo $this->render('_items_page.tpl'); ?>
  </div>
  <div class="like_list likes_week">
    <?php $this->likes = $this->week_likes; ?>
    <?php echo $this->render('_items_page.tpl'); ?>
  </div>
  <?php endif; ?>

</div>