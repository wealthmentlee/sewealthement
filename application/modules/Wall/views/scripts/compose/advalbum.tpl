<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: advalbum.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php
  $this->headScript()
    ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/composer_advalbum.js')
    ->appendFile($this->wallBaseUrl() . 'externals/fancyupload/Swiff.Uploader.js')
    ->appendFile($this->wallBaseUrl() . 'externals/fancyupload/Fx.ProgressBar.js')
    ->appendFile($this->wallBaseUrl() . 'externals/fancyupload/FancyUpload2.js')
?>

<script type="text/javascript">

  Wall.runonce.add(function (){

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");
    var type = 'wall';

    var composeInstance = feed.compose;

    if (composeInstance.options.type) type = composeInstance.options.type;

    composeInstance.addPlugin(new Wall.Composer.Plugin.Photo({
      title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
      lang : {
        'Add Photo' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
        'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Unable to upload photo. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload photo. Please click cancel and try again')) ?>'
      },
      requestOptions : {
        'url'  : en4.core.baseUrl + 'advalbum/album/compose-upload/type/'+type
      },
      fancyUploadOptions : {
        'url'  : en4.core.baseUrl + 'advalbum/album/compose-upload/format/json/type/'+type,
        'path' : en4.core.basePath + 'externals/fancyupload/Swiff.Uploader.swf'
      }
    }));
  });
</script>