<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: share.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php

$this->headScript()
        ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/core.js');


$services = array_keys(Engine_Api::_()->wall()->getManifestType('wall_service'));

$translate_list = array();

foreach ($services as $service){
  $translate_list[] = 'WALL_SHARE_' . strtoupper($service) . '';
  $translate_list[] = 'WALL_SHARE_' . strtoupper($service) . '_ACTIVE';
  $translate_list[] = 'WALL_STREAM_' . strtoupper($service) . '_LOGIN';
}

$this->headTranslate($translate_list);


?>

<script type="text/javascript">


  window.wallShareActive = function (service)
  {
    var serv_obj = window.parent.Wall.services.get(service);

    if (serv_obj.isEnabled()){

      Wall.request(en4.core.baseUrl + 'wall/index/service-share', {'provider': service, 'status': 1}, function (){});

      var $share = $$('.wall-share-' + service)[0];
      if ($share){
        $share.removeClass('disabled');
        $$('input[name="share['+service+']"]').set('value', 1);
        Wall.elements.get('Wall.Tips', (window.$uid || Slick.uidOf)($$('.wall-share-'+service)[0])).setTitle( en4.core.language.translate('WALL_SHARE_' + service.toUpperCase() + '_ACTIVE', serv_obj.options.object_name) );
      }

    }



  };

  var form_ajax = function (){

    $('wall-activity-share-form').getElement('form').addEvent('submit', function (e){

      e.stop();

      var loader = Wall.elementClass(Wall.OverLoader, $('body'), 'loader2', {is_smoothbox: true});
      loader.show();

      Wall.request('<?php echo $this->url(array('controller' => 'index', 'action' => 'share'), 'wall_extended', true)?>', $(this).toQueryString(), function (obj){

        loader.hide();

        if (window.parent && window.parent.Wall){

          window.parent.Wall.dialog.message(obj.message||obj.error, obj.status);

          if (obj.status){
            window.parent.Smoothbox.close();
          }
        }
        }.bind(this));

    });

    var $share = $$('.wallShareMenu a');

    $share.addEvent('click', function (e){

      e.stop();

      var service = $(this).get('rev');

      if ($(this).hasClass('disabled')){

        var serv_obj = window.parent.Wall.services.get(service);

        if (serv_obj.isEnabled()){
          window.wallShareActive(service);
        } else {
          window.parent.Wall.services.get(service).auth({'task': 'activity-share'});
        }

      } else {

        var title = en4.core.language.translate('WALL_SHARE_' +  service.toUpperCase());

        Wall.elements.get('Wall.Tips', (window.$uid || Slick.uidOf)($(this))).setTitle(title);
        $(this).getParent('.service').getElement('.share_input').set('value', 0);
        $(this).addClass('disabled');

        Wall.request(en4.core.baseUrl + 'wall/index/service-share', {'provider': service, 'status': 0}, function (){});

      }

    });

  };

  window.addEvent('load', function(){ form_ajax(); });

</script>


<div id="wall-activity-share-form" style="width: 600px;">

  <form method="post" action="<?php echo $this->url()?>" class="global_form_popup" style="margin-left:20px;margin-top:20px"
        enctype="application/x-www-form-urlencoded">
    <div>
      <div><h3><?php echo $this->translate('Share')?></h3>

        <p class="form-description"><?php echo $this->translate('Share this by re-posting it with your own message.')?></p>

        <div class="form-elements">
          <div class="form-wrapper" id="body-wrapper">
            <div class="form-label" id="body-label">&nbsp;</div>
            <div class="form-element" id="body-element">
              <textarea rows="6" cols="45" id="body" name="body"></textarea></div>
          </div>
          <div id="buttons-wrapper" class="form-wrapper">
            <div style="overflow: hidden;width: 315px;">
              <div style="float: left;">
                <fieldset id="fieldset-buttons">

                  <button type="submit" id="submit" name="submit"><?php echo $this->translate('Share')?></button>

                  <?php echo $this->translate('or')?>
                  <a onclick="parent.Smoothbox.close();" href="javascript:void(0);" type="button" id="cancel"
                     name="cancel"><?php echo $this->translate('cancel')?></a>

                  <input type="hidden" name="type" value="<?php echo $this->type;?>" />
                  <input type="hidden" name="id" value="<?php echo $this->id;?>" />

                </fieldset>
              </div>
              <div style="float: right;">
              <ul class="wallShareMenu">
                <?php

                  if ($this->viewer()->getIdentity()){

                    $setting = Engine_Api::_()->wall()->getUserSetting($this->viewer());

                    foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service){
                      $class = Engine_Api::_()->wall()->getServiceClass($service);
                      if (!$class || !$class->isActiveShare()) {
                        continue;
                      }

                      $a_class = 'wall-share-'.$service.' wall_tips';

                      $setting_key = 'share_' . $service . '_enabled';

                      $enabled = 0;
                      $object_name = '';
                      if (!empty($setting->$setting_key)){
                        $token = Engine_Api::_()->getDbTable('tokens', 'wall')->getUserToken($this->viewer(), $service);
                        if ($token && $token->check()){
                          $enabled = 1;
                          $object_name = $token->object_name;
                        }
                      }

                      if ($enabled){
                        $title = $this->translate('WALL_SHARE_' . strtoupper($service) . '_ACTIVE', array($object_name));
                      } else {
                        $title = $this->translate('WALL_SHARE_' . strtoupper($service) . '');
                        $a_class .= ' disabled';
                      }

                      echo '<li class="service">
                        <a href="javascript:void(0);" class="'.$a_class.'" rev="'.$service.'" title="'.$title.'"></a>
                        <input type="hidden" name="share['.$service.']" class="share_input" value="'.$enabled.'"/>
                      </li>';

                    }
                  }
              ?>
              </ul>
            </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>

  <br />
  <div class="sharebox">
    <?php if( $this->attachment->getPhotoUrl() ): ?>
      <div class="sharebox_photo">
        <?php echo $this->htmlLink($this->attachment->getHref(), $this->itemPhoto($this->attachment, 'thumb.icon'), array('target' => '_parent')) ?>
      </div>
    <?php endif; ?>
    <div>
      <div class="sharebox_title">
        <?php echo $this->htmlLink($this->attachment->getHref(), $this->attachment->getTitle(), array('target' => '_parent')) ?>
      </div>  
      <div class="sharebox_description">
        <?php echo $this->attachment->getDescription() ?>
      </div>
    </div>
  </div>
</div>


