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
//http://192.168.0.200/test2/widget/index/name/wall.feed?format=html&mode=recent&type=&list_id=0&subject=&feedOnly=true


$url = $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'wall.feed'), 'default', true)
?>



<?php

$tabs = Engine_Api::_()->wall()->getManifestType('wall_tabs');


// show only feed


  $tab_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.disabled'));
  $tab_default = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.default', 'social');

if( !$this->activity ){

}else{
 // echo '<script type="text/javascript">  window.last_id_hashtage = '.$this->activity[0]['action_id'].'; console.log(window.last_id_hashtage);</script>';


?>
  <script type="text/javascript" xmlns="http://www.w3.org/1999/html">

    function click_hashtags_closes(url){
      clearInterval(interval_id_hashtag);
      setInterval.clearAll();
      $$('.compose-content').set('text', " " );
      var wall_feed_id = $$('.wallFeed').get('id');
      Wall.feeds.items[wall_feed_id].loadFeed();

    }
    $$('.link_hashtag_div ').addEvent('mouseover', function(event) {
      event = new Event(event).stop();
      box = 1;
    });
    $$('.box_for_hashtag ').addEvent('mouseover', function(event) {
      event = new Event(event).stop();
      box = 1;
    });
    $$('.box_for_hashtag ').addEvent('mouseout', function(event) {
      event = new Event(event).stop();
      box = 0;
    });
    $$('.link_hashtag_div ').addEvent('mouseout', function(event) {
      event = new Event(event).stop();
      box = 0;
      setTimeout(function (){
        if(box != 1){
          $$('.box_for_hashtag ').addClass('display_none');
        }
      }, 500);
    });

    $$('.box_for_hashtag ').addEvent('mouseout', function(event) {
      event = new Event(event).stop();

      setTimeout(function (){
        if(box != 1){
          $$('.box_for_hashtag ').addClass('display_none');
        }
      }, 100);
    });

    //window.last_id_hashtage = <?php echo $this->activity[0]['action_id'] ?>; console.log(window.last_id_hashtage);

  update_options_hashtag = <?php echo $this->updateSettings;?>;

  </script>



  <div class="hashtag_composer_cont display_none" style="margin-top: 10px;">
    <?php if($this->update>0){

    }else{
   ?>
    <h3  class="h3_hashtag"> &nbsp;

      <div style=" display: inline-block;    float: left;    font-size: 16px; " ><?php echo $this->translate('HASHTAG_TITLE');?></div>

      <div class="hastag_result" id="hastag_result">
       <a href="javascript:void(0)" style=" cursor: default; font-size: 14px; font-weight: bold;"> #<?php echo  $this->name?> </a>
      </div>

      <div style="display: inline-block" ><a href="javascript:click_hashtags_closes('/test2/widget/index/name/wall.feed');"> <span class="hashtag_close"></span></a> </div>

    </h3>
<?php
}
?>
  </div>

<div id="hashtag-interval-check"></div>
<div id = "hashtag_contaner">




  <div id='opacity'>
        <?php if( $this->activity ):

          ?>
          <?php echo $this->wallActivityLoop($this->activity, array(
            'action_id' => $this->action_id,
            'viewAllComments' => $this->viewAllComments,
            'viewAllLikes' => $this->viewAllLikes,
            'comment_pagination' => $this->comment_pagination,
             'name' => $this->name
          ))?>
        <?php endif; ?>

        <?php if ((!empty($this->feed_config['next_page']) || $this->nextid) && !$this->endOfFeed):?>

          <li class="utility-viewall">
            <div class="pagination">
              <a href="javascript:void(0);" rev="<?php if (!empty($this->feed_config['next_page'])){?>page_<?php echo $this->feed_config['next_page']?><?php } else {?>next_<?php echo $this->nextid?><?php }?>"><?php echo $this->translate('View More');?></a>
            </div>
            <div class="loader" style="display: none;">
              <div class="wall_icon">&nbsp;</div>
              <div class="text">
                <?php echo $this->translate('Loading ...')?>
              </div>
            </div>
          </li>

        <?php endif;?>

        <?php if( !$this->activity ): ?>
        <?php endif; ?>

        <li class="utility-feed-config wall_displaynone" onclick='return(<?php echo Zend_Json::encode($this->feed_config)?>)'></li>

        <?php if ($this->firstid):?>
          <li class="utility-setlast" rev="item_<?php echo sprintf('%d', $this->firstid) ?>"></li>
        <?php endif;?>

  <?php

  if ($this->viewer()->getIdentity()){

    foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service){

      if (in_array($service, $tab_disabled)){
        continue ;
      }

      $class = Engine_Api::_()->wall()->getServiceClass($service);

      if (!$class || !$class->isActiveStream()){
        continue ;
      }

      $tpl = $class->getFeedTpl();



      echo "</li>";

    }

  }
  ?>
  </div>
  </div>
<?php
}
  ?>