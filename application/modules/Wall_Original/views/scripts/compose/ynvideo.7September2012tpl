<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ynvideo.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>
<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynvideo
 * @author     YouNet Company
 */
?>

<?php
    $this->headScript()
            ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Wall/externals/scripts/composer_ynvideo.js');

    $allowed = 0;
    $user = Engine_Api::_()->user()->getViewer();
    $allowed_upload = (bool) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('video', $user, 'upload');
    $ffmpeg_path = (bool) Engine_Api::_()->getApi('settings', 'core')->ynvideo_ffmpeg_path;
    if ($allowed_upload && $ffmpeg_path) {
        $allowed = 1;
    }
?>



<script type="text/javascript">
  Wall.runonce.add(function (){

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

    var type = 'wall';
    if (feed.compose.options.type) type = feed.compose.options.type;
    feed.compose.addPlugin(new Wall.Composer.Plugin.Ynvideo({
          title : '<?php echo $this->translate('Add Video') ?>',
          lang : {
              'Add Video' : '<?php echo $this->string()->escapeJavascript($this->translate('Add Video')) ?>',
              'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
              'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
              'Attach' : '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
              'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
              'Choose Source': '<?php echo $this->string()->escapeJavascript($this->translate('Choose Source')) ?>',
              'My Computer': '<?php echo $this->string()->escapeJavascript($this->translate('My Computer')) ?>',
              'YouTube': '<?php echo $this->string()->escapeJavascript($this->translate('YouTube Video')) ?>',
              'Vimeo': '<?php echo $this->string()->escapeJavascript($this->translate('Vimeo Video')) ?>',
              'To upload a video from your computer, please use our full uploader.': '<?php echo $this->string()->escapeJavascript($this->translate('To upload a video from your computer, please use our <a href="%1$s">full uploader</a>.', $this->url(array('action' => 'create', 'type' => 3), 'video_general'))) ?>'
          },
          allowed : <?php echo $allowed; ?>,
          type : type,
          requestOptions : {
              'url' : en4.core.baseUrl + 'ynvideo/index/compose-upload/format/json/c_type/' + type
          }
      }));
    });
</script>
