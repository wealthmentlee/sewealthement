<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2010-08-31 17:53 michael $
 * @author     Michael
 */
?>

<div class="pagereview_view_header">
  <span>
    <?php echo $this->translate('%1$s\'s Review', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>
  </span>
    <div class="backlink_wrapper">
        <a class="backlink" href="javascript:void(0);" onclick="Review.list();"><?php echo $this->translate('RATE_REVIEW_BACK'); ?></a>
    </div>
    <div class="clr"></div>
</div>

<div class="pagereview border">
    <div class="header">
        <?php echo $this->row->title?>
    </div>
    <div class="posted">
        <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($this->row->creation_date)?>
    </div>

    <div class="container">

        <?php

        foreach ($this->types as $type):?>

            <div class="review_stars_static view">
                <div class="rating">
                    <?php  print_arr($this->rating); echo $this->reviewRate($this->rating);?>

                </div>
                <!--<div class="title"><?php /*echo $type->label*/?></div>-->
                <div class="clr"></div>
            </div>
            <div class="clr"></div>

        <?php endforeach;?>

        <div style="clear:both;"></div>

    </div>

    <div class="body"><?php echo nl2br($this->BBCode($this->row->body))?></div>
</div>
<div class="pagereview border"></div>



<?php if (Engine_Api::_()->getDbTable('modules' ,'hecore')->isModuleEnabled('wall')):?>
    <?php echo $this->wallComments($this->row, $this->viewer()); ?>
<?php else: ?>
    <div id="pagereview_comments" class="comments"></div>
<?php endif;?>


