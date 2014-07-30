<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: tag.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php $this->headScript()
    ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/autocompleter/Autocompleter.js')
    ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/autocompleter/Autocompleter.Local.js')
    ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/autocompleter/Autocompleter.Request.js')
    ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/autocompleter/Observer.js')
    ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/composer_tag.js');

?>


<?php

$prepare_local = false;

$select = Engine_Api::_()->wall()->getTagSuggest($this->viewer());
$paginator = Zend_Paginator::factory($select);

if ($paginator->getTotalItemCount() < 500){

  $prepare_local = array();

  $paginator->setItemCountPerPage(500);
  foreach (Engine_Api::_()->wall()->getItems($paginator->getCurrentItems()) as $item){
    $prepare_local[] = array(
      'type'  => $item->getType(),
      'id'    => $item->getIdentity(),
      'guid'  => $item->getGuid(),
      'label' => $item->getTitle(),
      'photo' => $this->itemPhoto($item, 'thumb.icon'),
      'url'   => $item->getHref(),
    );
  }

}

?>

<script type="text/javascript">

  Wall.runonce.add(function (){
    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");
    var tag = new Wall.Composer.Plugin.Tag({
      <?php if ($prepare_local !== false):?>
      'suggestProto' : 'local',
      'suggestParam' : <?php echo Zend_Json::encode($prepare_local) ?>
      <?php endif;?>
    });

    feed.compose.addPlugin(tag);
  });

</script>