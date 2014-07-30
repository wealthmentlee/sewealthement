<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: smile.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php $this->headScript()
    ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/composer_smile.js');

?>

<?php

if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.smile', 1)):

?>


<script type="text/javascript">

  Wall.runonce.add(function (){
    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");
    var tag = new Wall.Composer.Plugin.Smile({
      smiles: <?php echo $this->wallSmiles()->getJson()?>,
      title : '<?php echo $this->translate('Add Smile');?>',
      lang : {}
    });

    feed.compose.addPlugin(tag);
  });

</script>


<?php endif;?>