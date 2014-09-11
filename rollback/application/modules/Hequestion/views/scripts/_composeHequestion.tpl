<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _composeHequestion.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>



<?php

$request = Zend_Controller_Front::getInstance()->getRequest();
$is_lineline = 0;
if ($request->getModuleName() == 'timeline' && $request->getControllerName() == 'profile' && $request->getActionName() == 'index'){
  $is_lineline = 1;
}



if ($is_lineline){
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Timeline/externals/scripts/wall_core.js');
} else {
  $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Wall/externals/scripts/core.js');
}

$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hequestion/externals/scripts/core.js')
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Hequestion/externals/scripts/composer_hequestion.js')
    ;



?>


<?php
  $this->headTranslate(array('HEQUESTION_ADD_OPTION'));
?>




<script type="text/javascript">

  Wall.runonce.add(function (){

    try {

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

    feed.compose.addPlugin(new Wall.Composer.Plugin.Hequestion({
      title: '<?php echo $this->string()->escapeJavascript($this->translate('Add Question')) ?>',
      lang : {
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>'
      },
      html: <?php echo $this->jsonInline($this->render('_wallComposer.tpl'));?>,
      max_option: <?php echo (int) Engine_Api::_()->getDbTable('settings', 'core')->getSetting('hequestion.maxoptions', 15);?>,
      is_timeline: <?php echo $is_lineline; ?>
    }));

    } catch (e){
      alert(e);
    }

  });

</script>

