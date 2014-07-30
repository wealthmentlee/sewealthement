<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: avp.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php $this->headScript()->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/composer_avp.js') ?>
<?php
    $allowed = 1;
    $user = Engine_Api::_()->user()->getViewer();
    if ($user->getIdentity() < 1) $allowed = 0;
    
    if ($allowed)
    {
        $plugin = new Avp_Plugin_Menus();
        
        $allowed_upload = $plugin->onMenuInitialize_AvpMainUpload(array());
        $allowed_import = $plugin->onMenuInitialize_AvpMainImport(array());
        
        if ($allowed_upload || $allowed_import) $allowed = 1;
    }
?>



<script type="text/javascript">

  Wall.runonce.add(function (){

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

    var type = 'wall';
    if (feed.compose.options.type) type = feed.compose.options.type;
    feed.compose.addPlugin(new Wall.Composer.Plugin.AVP(
    {
      title : '<?php echo $this->translate('Add Video') ?>',
      allowed : <?php echo (int)$allowed;?>,
      import_allowed : <?php echo (int)$allowed_import;?>,
      upload_allowed : <?php echo (int)$allowed_upload;?>,
      upload_title : '<?php echo $this->translate('WALL_Upload') ?>',
      import_title : '<?php echo $this->translate('WALL_Import') ?>',
      lang : {
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>'
      }
    }));

  });

</script>
