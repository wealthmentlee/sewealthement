<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: see-liked.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<?php if ($this->error): ?>
  <div class="contacts_error no_content"><?php echo $this->translate($this->error); ?></div>
<?php else: ?>

<?php $this->headScript()->appendFile('application/modules/Like/externals/scripts/core.js'); ?>

<div id="he_contacts_loading" class="hidden">&nbsp;</div>

<div class="he_contacts">
  <h4 class="contacts_header"><?php echo $this->translate("like_%s's likes", $this->subject->getTitle()); ?></h4>
  <?php if (!$this->viewer->isSelf($this->subject)): ?>
    <div class="options">
      <div class="select_btns">
        <a href="javascript:void(0)" class="active" onClick="like.select_list('all', <?php echo $this->subject->getIdentity(); ?>, this);">
          <?php echo $this->translate("like_All Likes"); ?>
        </a>
        <a href="javascript:void(0)" class="" onClick="like.select_list('mutual', <?php echo $this->subject->getIdentity(); ?>, this);">
          <?php echo $this->translate("like_Mutual Likes"); ?>
        </a>
      </div>
    <div class="clr"></div>
  </div>
  <?php endif; ?>
  <div class="clr"></div>
  <div class="contacts">
    <div id="he_list">
     <?php echo $this->render('_composeItems.tpl'); ?>
    </div>
    <div class="clr"></div>
  </div>
  <div class="clr"></div>
</div>

<script type="text/javascript">
en4.core.runonce.add(function(){
  like.init_buttons();
});
</script>

<?php endif; ?>