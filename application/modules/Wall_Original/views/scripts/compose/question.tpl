<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: question.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>
<?php if (!Engine_Api::_()->core()->hasSubject()):?>
<?php
  $this->headScript()
      ->appendFile($this->baseUrl() . '/application/modules/Wall/externals/scripts/composer_question.js')
  ;

  ?>

<script type="text/javascript">
  Wall.runonce.add(function (){
    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");
    var composeInstance = feed.compose;

    if (composeInstance.options.type != 'message') {
      var type = 'wall';
      if (composeInstance.options.type) type = composeInstance.options.type;
      composeInstance.addPlugin(new Wall.Composer.Plugin.Question({
        title : '<?php echo $this->translate('Ask') ?>',
        lang : {

        }
      }));
    }
  });
</script>
<?php endif;?>