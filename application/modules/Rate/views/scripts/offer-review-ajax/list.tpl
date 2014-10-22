<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2010-08-31 17:53 michael $
 * @author     Michael
 */
?>

<?php if ($this->paginator->count() > 0): ?>
  <?php foreach ($this->paginator as $counter => $row): ?>
    <div class="offerreview<?php if ($counter !=0 ):?> border<?php endif; if ($row['is_owner']):?> owner<?php endif;?>">
      <div class="header">
        <a onclick='OfferReview.view(<?php echo $row['offerreview_id']?>); return false;' href="javascript:void(0)">
          <?php echo $row['title'] ?></a>
        <div style="float: right;">
          <?php if ($row['is_owner']): echo $this->htmlLink('javascript:OfferReview.edit('.$row['offerreview_id'].');', $this->htmlImage($this->baseUrl().'/application/modules/Rate/externals/images/edit16.png', '', array('border' => 0)), array('title' => $this->translate('edit')) ); endif; ?>
          <?php if ($row['is_owner'] || $this->isAllowedRemove): echo $this->htmlLink('javascript:OfferReview.remove('.$row['offerreview_id'].');', $this->htmlImage($this->baseUrl().'/application/modules/Rate/externals/images/delete16.png', '', array('border' => 0)), array('title' => $this->translate('delete'))); endif; ?>
        </div>
      </div>
      <div class="posted">
        <?php if ($this->countOptions): ?>
          <div class="he_rate_small_cont">
            <?php echo $this->reviewRate($row['rating'], true)?> <div class="offerreview_count">(<?php echo round($row['rating'],2)?>)</div>
            <div class="clr"></div>
          </div>
        <?php endif;?>
        <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($row->creation_date)?>
      </div>
      <div class="body"><?php echo Engine_String::substr($row->body, 0, 350); if (Engine_String::strlen($row->body)>349): echo $this->translate("..."); endif;?></div>
    </div>
  <?php endforeach; ?>
  <br />

  <?php if ($this->paginator->count() > 1): ?>
    <?php echo $this->paginationControl($this->paginator, null, array("pagination.tpl","rate"), array(
      'offer' => $this->offer
    ))?>
  <?php endif?>

  <?php if ($this->isAllowedPost):?>
    <a href="javascript:OfferReview.goCreate();" class="offerreview_create"><?php echo $this->translate('RATE_REVIEW_CREATE')?></a>
  <?php endif; ?>
<?php else: ?>
  <div class="tip">
    <span>
      <?php if(!$this->subject->isSubscribed($this->viewer)): ?>
        <?php echo $this->translate('RATE_OFFERSREVIEW_TIP');?>
      <?php endif; ?>
      <?php if ($this->isAllowedPost):?>
        <?php echo $this->translate('RATE_REVIEW_TIP_CREATE',  '<a href="javascript:void(0);" onClick="OfferReview.create();">', '</a>'); ?>
      <?php endif; ?>
    </span>
  </div>
<?php endif; ?>

