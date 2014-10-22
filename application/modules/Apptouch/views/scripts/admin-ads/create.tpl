<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-12-03 11:18:13 ulan t $
 * @author     Ulan
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>

<script type="text/javascript">
    window.addEvent('load', function () {
        SortablesInstance = new Sortables('menu_list', {
            clone:true,
            constrain:false,
            handle:'.item_label',
            onComplete:function (e) {
                reorder(e);
            }
        });
    });
</script>

<?php echo $this->content()->renderWidget('apptouch.admin-main-menu', array('active' => 'apptouch_admin_main_ads')) ?>
<h3><?php
  if($this->ad)
    echo $this->translate("APPTOUCH_Edit Ad:") . $this->ad->name;
  else
    echo $this->translate("APPTOUCH_Create New Ad");
  ?></h3>

<br>
<div class="settings">
  <div class="admin_home_middle">
    <div class="block-col-2">
        <?php echo $this->render("admin/_adPreview.tpl"); ?>
        <?php echo $this->form->render($this); ?>
    </div>
  </div>
</div>

<script type="text/javascript">
  $$('#anim_delay, #anim_duration').addEvent('keypress', function(e){
    var c = e.code;
    if(c != 8 && c != 46 && (c < 48 || c > 57) ){
      return false;
    }
  });
  var adCampaignSelected = function(el){
    if(el.getElement('option:selected').value !== ''){
      $('form-upload').removeClass('adc-not-selected');
    } else {
      $('form-upload').addClass('adc-not-selected');
    }

  };
  <?php if($this->ad) { ?>
  setPos(<?php echo $this->ad->position;?>);
  setFixed(<?php echo $this->ad->fixed;?>);
  <?php } ?>
  adCampaignSelected($('ad_campaign'));
    /* Switch the active menu item */
  var updateTextFields = function() {
    if ($$('#mediatype-0:checked').length) {
      $('upload_image-wrapper').setStyle('display', 'block');
      $('file-wrapper').setStyle('display', 'block');
      $('html_field-wrapper').setStyle('display', 'none');
      $('submit-wrapper').setStyle('display', 'none');
    } else if ($$('#mediatype-1:checked').length) {
      $('upload_image-wrapper').setStyle('display', 'none');
      $('html_field-wrapper').setStyle('display', 'block');
      $('submit-wrapper').setStyle('display', 'block');
    } else {
      $('upload_image-wrapper').setStyle('display', 'none');
      $('html_field-wrapper').setStyle('display', 'none');
      $('submit-wrapper').setStyle('display', 'none');
    }
  }

  en4.core.runonce.add(updateTextFields);
</script>