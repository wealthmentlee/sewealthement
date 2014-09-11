<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ynmusic.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php
$this->headScript()
    ->appendFile($this->wallBaseUrl() . 'application/modules/Ynmusic/externals/soundmanager/script/soundmanager2.js')
    ->appendFile($this->wallBaseUrl() . 'application/modules/Ynmusic/externals/scripts/core.js')
    ->appendFile($this->wallBaseUrl() . 'application/modules/Ynmusic/externals/scripts/player.js')
    ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/composer_ynmusic.js')
    ->appendFile($this->wallBaseUrl() . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($this->wallBaseUrl() . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($this->wallBaseUrl() . 'externals/fancyupload/FancyUpload2.js')
?>
<script type="text/javascript">
  
  Wall.runonce.add(function (){

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");
    var composeInstance = feed.compose;

    var type = 'wall';
    if (composeInstance.options.type) type = composeInstance.options.type;
    composeInstance.addPlugin(new Wall.Composer.Plugin.Ynmusic({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Music')) ?>',
      lang : {
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Loading song, please wait...': '<?php echo $this->string()->escapeJavascript($this->translate('Loading song, please wait...')) ?>',
        'Unable to upload music. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload music. Please click cancel and try again')) ?>',
        'Song got lost in the mail. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Song got lost in the mail. Please click cancel and try again')) ?>'
      },
      requestOptions : {
        'url'  : en4.core.baseUrl  + 'ynmusic/album/edit-add-song/album_id/-1/format/json?ul=1'+'&type='+type
      },
      fancyUploadOptions : {
        'url'  : en4.core.baseUrl  + 'ynmusic/album/edit-add-song/album_id/-1/format/json?ul=1'+'&type='+type,
        'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
      }
    }));
  });
</script>

