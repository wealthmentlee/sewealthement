<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-12-03 11:18:13 ulan t $
 * @author     Ulan T
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
<h3><?php echo $this->translate("APPTOUCH_Ad Campaign Manager") ?></h3>

<br>
<div class="admin_layoutbox_menu">
    <ul>
        <li class="admin_layoutbox_menu_generic" id="admin_layoutbox_menu_openpage">
            <div class="admin_layoutbox_menu_wrapper_generic admin_layoutbox_menu_pages_wrapper" id="admin_layoutbox_menu_pages_wrapper">
                <ul class="admin_layoutbox_menu_pages">
                  <?php foreach( $this->allPages as $page ): ?>
                        <li onclick="loadPage(<?php echo $page->page_id ?>);">
                          <?php echo $page->displayname ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));">
                <span><?php echo $this->translate("Editing:"); ?></span>
              <?php echo $this->pageObject->displayname ?><span class="more">&nbsp;</span>
            </a>
        </li>
<!--        <li id="admin_layoutbox_menu_savechanges">-->
<!--            <a href="javascript:void(0);" onClick="saveChanges()">-->
<!--              --><?php //echo $this->translate("Save Changes") ?>
<!--            </a>-->
<!--        </li>-->
    </ul>
</div>
<div class="settings">
  <div class="admin_home_middle">
    <div class="block-col-2">
      <?php echo $this->render("admin/_adPreview.tpl"); ?>
      <?php echo $this->form->render($this); ?>
    </div>
  </div>
</div>

<script type="text/javascript">
  var setAdEnable = function(el){
    var $els = $$('#adcampaign_id-wrapper, #advs-wrapper, #add_new_adv-wrapper');

    if(el.checked){
      $els.setStyle('display', 'block');
    }else
      $els.setStyle('display', 'none');
  }
  setAdEnable($('enable_ad'));
  var adCampaignSelected = function(el){
    if(el.getElement('option:selected').value !== ''){
      $('advs-element').innerHTML = '<div class="loader adv-banner"></div>';
      var val = el.getElement('option:selected').value;
      var seperator = 'ad_campaign/';
      var addel = $$('#add_new_adv-element a')[0];
      var hrefarr = addel.href.split(seperator);
      hrefarr[1] = val;
      addel.href = hrefarr.join(seperator);
      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'admin/apptouch/ads/get-ads',
        data : {
          campaign_id: val,
          format : 'json'
        },
        onSuccess : function(response){
          var advsContainer = $('advs-element');
          var l = response.ads.length;
          var ht = '';
          if(l)
            for(var i = 0; i < l; i ++){
              var ad = response.ads[i];
              ht += '<div class="adv-banner" id="advid_' + ad.ad_id + '"><p onclick="play(this)" position="' + ad.position + '" fixed="' + ad.fixed + '" anim-type="' + ad.anim_type + '" anim-delay="' + ad.anim_delay + '" anim-duration="' + ad.anim_duration + '" ><?php echo $this->translate('APPTOUCH_Play') ?> ' + ad.html_code + '</p><span>' + ad.name + '</span></div>';
            }
          else
            ht = '<div class="adv-banner no-adv"><?php echo $this->translate('APPTOUCH_NO Banners') ?></div>';
          advsContainer.innerHTML = ht;
          setAdEnable($('enable_ad'));
        }
      }));
      $('add_new_adv-wrapper').getElement('.create_new_adv').setStyle('display', 'inline');
      $('advs-wrapper').setStyle('display', 'block');
    } else {
      $('add_new_adv-wrapper').getElement('.create_new_adv').setStyle('display', 'none');
      $('advs-wrapper').setStyle('display', 'none');
      $('advs-element').innerHTML = '';
    }

  };
  var play = function(el){
    var pos = parseInt(el.get('position'));
    var fixed = parseInt(el.get('fixed'));
    var anim = {};
    anim.type = el.get('anim-type');
    anim.delay = 0;
    anim.duration = el.get('anim-duration');
    var adv = $$('.content-adv')[0];
    adv.setStyle('display', 'block');
    adv.innerHTML = '';
    adv.grab(el.getElement('a').clone(true).show());
    setPos(pos);
    setFixed(fixed);
    if(fixed)
      preview(anim)

  };
  adCampaignSelected($('adcampaign_id'));
    /* Switch the active menu item */
    var switchPageMenu = function(event, activator) {
        var element = activator.getParent('li');
        $$('.admin_layoutbox_menu_generic').each(function(otherElement) {
            var otherWrapper = otherElement.getElement('.admin_layoutbox_menu_wrapper_generic');
            if( otherElement.get('id') == element.get('id') && !otherElement.hasClass('active') ) {
                otherElement.addClass('active');
                otherWrapper.setStyle('display', 'block');
                var firstInput = otherElement.getElement('input');
                if( firstInput ) {
                    firstInput.focus();
                }
            } else {
                otherElement.removeClass('active');
                otherWrapper.setStyle('display', 'none');
            }
        });
    };

    /* Load a different page */
    var loadPage = function(page_id) {
      window.location.search = '?page=' + page_id;
    };

    var saveChanges = function()
    {
      var data = {};
      var inputs = $$('#apptouch_layout_form input');
      inputs.each(function(item, index){
      });
    };
</script>