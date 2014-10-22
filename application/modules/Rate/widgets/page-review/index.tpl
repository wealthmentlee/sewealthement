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
  ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Rate/externals/scripts/Review.js');

?>

<script type="text/javascript">

  en4.core.runonce.add(function (){
    Review.pageId = <?php echo $this->pageId?>;
    Review.url.create = '<?php echo $this->url(array('action' => 'create'), 'page_review')?>';
    Review.url.edit = '<?php echo $this->url(array('action' => 'edit'), 'page_review')?>';
    Review.url.list = '<?php echo $this->url(array('action' => 'list'), 'page_review')?>';
    Review.url.remove = '<?php echo $this->url(array('action' => 'remove'), 'page_review')?>';
    Review.url.view = '<?php echo $this->url(array('action' => 'view'), 'page_review')?>';
    Review.allowedComment = <?php echo (int)(bool)$this->viewer->getIdentity()?>;
    Review.init();
    <?php echo implode(" ", $this->js)?>
  });

  window.addEvent('domready', function(){
    <?php echo $this->init_js; ?>
  });

</script>

<div class="pagerate_loader hidden" id="pagerate_loader">
  <?php echo $this->htmlImage($this->layout()->staticBaseUrl . 'application/modules/Rate/externals/images/loader.gif'); ?>
</div>
<div class="clr"></div>

<div class="pagereview_container_message hidden">
  <ul class="success form-notices" style="margin-top:0;"><li></li></ul>
  <ul class="error form-errors" style="margin-top:0;"><li></li></ul>
</div>



<div class="pagereview_container_list">
  <?php  echo $this->render('list.tpl'); ?>
</div>


<div class="pagereview_container_create hidden">
  <?php echo $this->form->render()?>
</div>

<div class="pagereview_container_edit hidden"></div>
<div class="pagereview_container_view hidden"></div>
