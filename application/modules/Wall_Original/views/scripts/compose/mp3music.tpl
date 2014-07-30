<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: mp3music.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>
<?php if(Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('mp3music_album', null, 'create')->checkRequire()):?>
<?php
  $this->headScript()
      ->appendFile($this->wallBaseUrl() . 'externals/soundmanager/script/soundmanager2'
           . (APPLICATION_ENV == 'production' ? '-nodebug-jsmin' : '' ) . '.js')
    ->appendFile($this->wallBaseUrl() . 'application/modules/Mp3music/externals/scripts/core.js')
    ->appendFile($this->wallBaseUrl() . 'application/modules/Mp3music/externals/scripts/player.js')
    ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/composer_mp3music.js')
    ->appendFile($this->wallBaseUrl() . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($this->wallBaseUrl() . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($this->wallBaseUrl() . 'externals/fancyupload/FancyUpload2.js')
?>
<script type="text/javascript">
  Wall.runonce.add(function (){

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

    var type = 'wall';
    if (feed.compose.options.type) type = feed.compose.options.type;
    feed.compose.addPlugin(new Wall.Composer.Plugin.Mp3music({
      
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Mp3 Music')) ?>',
      lang : {
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Loading song, please wait...': '<?php echo $this->string()->escapeJavascript($this->translate('Loading song, please wait...')) ?>',
        'Unable to upload music. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload music. Please click cancel and try again')) ?>',
        'Song got lost in the mail. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Song got lost in the mail. Please click cancel and try again')) ?>'
      },
      requestOptions : {
       'url'  : en4.core.baseUrl  + 'mp3music/album/edit-add-song/album_id/-1/format/json?ul=1'+'&type='+type
      },
      fancyUploadOptions : {
        'url'  : en4.core.baseUrl  + 'mp3music/album/edit-add-song/album_id/-1/format/json?ul=1'+'&type='+type,
        'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
      }
    }));
  });
</script>

<?php endif;?>