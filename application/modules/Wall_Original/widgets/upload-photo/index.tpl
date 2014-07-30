<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php
echo $this->render('_header.tpl');
?>

<script type="text/javascript">

  window.wallUploadPhoto = function (new_photo)
  {
    $$('.wall-photo-upload .current-photo').set('html', new_photo);
  };


  en4.core.runonce.add(function (){

    $$('.layout_wall_upload_photo .global_form #Filedata').addEvent('change', function (){
      (new Element('iframe', {'style': 'display:none', 'src': en4.core.baseUrl + 'wall/upload-photo/upload', 'id': 'wall-upload-photo'})).inject(Wall.externalDiv());
      $(this).getParent('form').set('target', 'wall-upload-photo').submit();
    });

    $$('.layout_wall_upload_photo .wall_take_photo').addEvent('click', function () {
      var camera = new Wall.Camera({
        url: en4.core.baseUrl + 'wall/upload-photo/camera'
      });
      camera.addEvent('onSuccess', function (obj){
        if (obj.save){
          $$('.wall-photo-upload .current-photo img').set('src', obj.photo_url);
        }
      });
    });

  });

</script>

<div class="wall-photo-upload">
  <div class="current-photo">
    <?php echo $this->itemPhoto($this->viewer, 'thumb.profile');?>
  </div>
  <div class="photo-options">

    <div>
      <a href="<?php echo $this->url(array('module' => 'wall', 'controller' => 'upload-photo', 'action' => 'index'), 'default', true);?>" class="smoothbox wall-button wall_upload">
        <span class="wall_icon">&nbsp;</span>
        <?php echo $this->translate('WALL_Upload a Photo');?>
      </a>
    </div>
    <div>
      <?php echo $this->translate('WALL_From your computer');?>
    </div>

    <div><?php echo $this->translate('WALL_OR');?></div>

    <div>
      <a href="javascript:void(0);" class="wall_take_photo wall-button wall_take">
        <span class="wall_icon">&nbsp;</span>
        <?php echo $this->translate('WALL_Take a Photo');?>
      </a>
    </div>
    <div>
      <?php echo $this->translate('WALL_With your webcam');?>
    </div>
  </div>
</div>