<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 19:53 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

$this->headScript()
  ->appendFile('application/modules/Rate/externals/scripts/OfferReview.js');

?>

<script type="text/javascript">

  en4.core.runonce.add(function () {
    OfferReview.offerId = <?php echo $this->offerId?>;
    OfferReview.url.create = '<?php echo $this->url(array('action' => 'create'), 'offer_review')?>';
    OfferReview.url.edit = '<?php echo $this->url(array('action' => 'edit'), 'offer_review')?>';
    OfferReview.url.list = '<?php echo $this->url(array('action' => 'list'), 'offer_review')?>';
    OfferReview.url.remove = '<?php echo $this->url(array('action' => 'remove'), 'offer_review')?>';
    OfferReview.url.view = '<?php echo $this->url(array('action' => 'view'), 'offer_review')?>';
    OfferReview.allowedComment = <?php echo (int)(bool)$this->viewer->getIdentity()?>;
    OfferReview.init();
  <?php echo implode(" ", $this->js)?>
  });

</script>

<div class="offerrate_loader hidden" id="offerrate_loader">
  <?php echo $this->htmlImage($this->baseUrl() . '/application/modules/Rate/externals/images/loader.gif'); ?>
</div>
<div class="clr"></div>

<div class="offerreview_container_message hidden">
  <ul class="success form-notices" style="margin-top:0;">
    <li></li>
  </ul>
  <ul class="error form-errors" style="margin-top:0;">
    <li></li>
  </ul>
</div>

<div class="offerreview_container_list">
  <?php  echo $this->render('list.tpl'); ?>
</div>
<div class="offerreview_container_create hidden">
  <?php echo $this->form->render()?>
</div>

<div class="offerreview_container_edit hidden"></div>
<div class="offerreview_container_view hidden"></div>

