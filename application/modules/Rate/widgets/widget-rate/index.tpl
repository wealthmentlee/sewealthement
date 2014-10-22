<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 19:14 ermek $
 * @author     Ermek
 */

if ($this->isReview): ?>

<script type="text/javascript">

  en4.core.runonce.add(function (){
    // is widget
    if (window.Review){
      var $stars = $$('.review_widget .rate_star');
      $stars.addClass('active_change');
      $stars.addEvent('click', function (){
        tabContainerSwitch($$('.tab_layout_rate_page_review')[0], 'generic_layout_container layout_rate_page_review');
      });
    }
  });

</script>
    
    
<div class="reviews_container">

<?php

foreach ($this->types as $type):?>

<div class="review_stars_static review_widget">
  <div class="rating" title="<?php echo $this->translate('RATE_REVIEW_Add review'); ?>">
    <?php echo $this->reviewRate($type->value)?>
  </div>
  <div class="title"><?php echo $type->label?> (<?php echo $type->value?>)</div>
  <div style="clear:both;"></div>
</div>

<?php endforeach;?>

</div>
<div class="clr"></div>

<?php else: ?>

<?php
    $total_items = count($this->objs);
    $counter = 1;

    $lang_vars = $this->jsonInline(array(
      'title' => $this->translate('Who has voted?'),
      'list_title1' => $this->translate('Everyone'),
      'list_title2' => $this->translate('Friends')
    ));
?>

<script type="text/javascript">
en4.core.runonce.add(function(){
    var rateVar = new Rate(<?php echo "{$this->item_id}, '{$this->item_type}', '{$this->rate_uid}', {$this->can_rate}"; ?>);
    rateVar.rate_url = '<?php echo $this->rate_url; ?>';
    rateVar.langvars = <?php echo $lang_vars; ?>;
});
</script>

<div class="he_rate_cont" id="rate_uid_<?php echo $this->rate_uid; ?>">
    <div class="rate_stars_cont" style="width: <?php echo 28*$this->maxRate?>px;">
        <?php for ($i = 0; $i < $this->maxRate; $i++){
                  if (($i + 0.125) > $this->item_score) {
                    $star_value = 'no_rate';
                  } else if (($i + 0.375) > $this->item_score) {
                    $star_value = 'quarter_rated';
                  } else if (($i + 0.625) > $this->item_score) {
                    $star_value = 'half_rated';
                  } else if (($i + 0.875) > $this->item_score) {
                    $star_value = 'fquarter_rated';
                  } else {
                    $star_value = 'rated';
                  }
        ?>

        <div class="rate_star <?php echo $star_value;?>" id="rate_star_<?php echo ($i + 1)?>"></div>
        <?php }?>
    </div>
    <div class="item_rate_info">
        <?php $this->translate('Score:') ?> <span class="item_score"><?php echo $this->item_score?>/<?php echo $this->maxRate?></span>
        <span class="item_votes"><?php echo ($this->rate_info) ? $this->rate_info['rate_count'] : 0; ?></span>
        <a class="item_voters" href="javascript://"><?php echo $this->translate(array('vote', 'votes', (($this->rate_info) ? $this->rate_info['rate_count'] : 0))); ?></a>
    </div>
    <div class="rate_loading display_none"><span class="rate_loader"><?php $this->translate('Loading ...') ?></span></div>
</div>
<div class="clr"></div>

<?php endif?>