<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     bolot
 */
$wealthApi = Engine_Api::_()->wealthment();
?>
<!--[if IE]>
<style>
.pinfeed .wall-action-item { {
  border: 1px solid #BBBBBB;
  }
.layout_left .generic_layout_container {
  border: 1px solid #BBBBBB\0/;
}
</style>
<![endif]-->
<style type="text/css">

  .wall-stream {
    margin: -20px 0 !important;
  }
  .layout_left .generic_layout_container{
    border: 1px solid #BBBBBB\0/;
    box-shadow: 0 2px 4px rgba(30, 30, 30, 0.4);
    display: block;
    float: right;
    margin-top: 13px;
    padding: 20px 9px 9px;
    width: 200px;
  }
  h3{
    background: none;
    border-radius: 3px 3px 3px 3px;
    font-size: 1.2em;
    padding: 0.4em 0.7em;
  }
  .video_info{
    clear: both;
  }
  .feed_item_attachments .video_thumb_wrapper {
    max-width: 202px;

  }
  .thumb_video_activity {
    max-width: 190px;
  width: 189px;
  }
  <?php

  if($this->width==1){
  ?>
  .layout_active_theme_modern .layout_left{
    margin-right: 15px !important;
  }

  #global_content{
    width: 100% !important;
  }
  .layout_left {
    margin-left: 80px;

  }

.layout_middle {
       margin-right: 80px;

     }
  .wallFeed {
    margin: 10px 0px 0px 0px !important

  }
  .layout_active_theme_clean .wallFeed {
    margin: 0px !important

  }
  .layout_active_theme_modern .wallFeed {
    margin: -10px 0 0 !important;
  }
  .layout_left .generic_layout_container {
    margin-top: 0px !important;
    margin-bottom: 15px;

  }
  <?
  }else{
  ?>
#activity-feed{
  margin-top: 25px;
}
  <?php
  }
  ?>
  .layout_left .generic_layout_container {

  }
</style>
<script type="text/javascript">
  <?php if ($this->width == 1){
?>
  width_res = 1;
  <?
  }else{

  ?>
  width_res = 0;
  <?php
  }
  ?>

</script>

<?php
$pin = 1;
if( !empty($this->feedOnly) && empty($this->checkUpdate)): // ajax?>

  <?php echo $this->wallActivityLoop($this->activity, array(
    'action_id' => $this->action_id,
    'viewAllComments' => $this->viewAllComments,
    'viewAllLikes' => $this->viewAllLikes,
    'comment_pagination' => $this->comment_pagination,
    'module' => 'pinfeed',
  ));?>


  <?php if ((!empty($this->feed_config['next_page']) || $this->nextid) && !$this->endOfFeed):?>

    <li class="utility-viewall">
      <div class="pagination">
        <a href="javascript:void(0);" rev="<?php if (!empty($this->feed_config['next_page'])){?>page_<?php echo $this->feed_config['next_page']?><?php } else {?>next_<?php echo $this->nextid?><?php }?>"><?php echo $this->translate('View More');?></a>
      </div>
      <div class="loader" style="display: none;">
        <div class="wall_icon"></div>
        <div class="text">
          <?php echo $this->translate('Loading ...')?>
        </div>
      </div>
    </li>

  <?php endif;?>

  <li class="utility-feed-config wall_displaynone" onclick='return(<?php echo Zend_Json::encode($this->feed_config)?>)'></li>

  <?php if ($this->firstid):?>
    <li class="utility-setlast wall_displaynone" rev="item_<?php echo sprintf('%d', $this->firstid) ?>"></li>
  <?php endif;?>


  <script type="text/javascript">



        // Prepare layout options.
    if($('pinfeed')){
      var options = {
        autoResize: true, // This will auto-update the layout when the browser window is resized.
        container: $('pinfeed'),
        item: $$('.wall-action-item'),
        offset: 2,
        itemWidth: 255,
        bottom: 1
      };
      var handler = $$('.wall-action-item');
      pinfeed(options);
    }
  </script>
  <?php return; ?>
<?php endif; ?>



<?php if (!empty($this->checkUpdate)):?>

  <?php if ($this->activityCount):?>

    <li class="utility-getlast">

      <script type='text/javascript'>
        Wall.activityCount(<?php echo $this->activityCount?>);
      </script>

      <div class='tip'>
        <span>
          <a href='javascript:void(0);' class="link">
            <?php echo $this->translate(array(
                '%d new update is available - click this to show it.',
                '%d new updates are available - click this to show them.',
                $this->activityCount),
              $this->activityCount)?>
          </a>
        </span>
      </div>

      <?php return; ?>

    </li>

  <?php endif; ?>
  <?php return ;?>

<?php endif;?>



<?php
echo $this->render('_header.tpl');
?>

<div id="wall-feed-scripts">
  <script type="text/javascript">


    Wall.runonce.add(function (){

      var feed = new Wall.Feed({
        feed_uid: '<?php echo $this->feed_uid?>',
        enableComposer: <?php echo ($this->enableComposer) ? 1 : 0?>,
        url_wall: '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'wall.feed'), 'default', true) ?>',
        last_id: <?php echo sprintf('%d', $this->firstid) ?>,
        subject_guid : '<?php echo $this->subjectGuid ?>',
        fbpage_id: <?php echo ($this->fbpage_id) ? "'{$this->fbpage_id}'" : 0;?>
      });

      feed.params = <?php echo $this->jsonInline($this->list_params);?>;

      <?php if (empty($this->action_id)):?>

      <?php if ($this->updateSettings):?>

      feed.watcher = new Wall.UpdateHandler({
        baseUrl : en4.core.baseUrl,
        basePath : en4.core.basePath,
        identity : 4,
        delay : <?php echo $this->updateSettings;?>,
        last_id: <?php echo sprintf('%d', $this->firstid) ?>,
        subject_guid : '<?php echo $this->subjectGuid ?>',
        feed_uid: '<?php echo $this->feed_uid?>'
      });
      try {
        setTimeout(function (){
          feed.watcher.start();
        },1250);
      } catch( e ) {}

      <?php endif;?>

      <?php else:?>

      var tab_link = $$('.tab_layout_wall_feed')[0];
      if (tab_link && tabContainerSwitch){
        tabContainerSwitch(tab_link, 'generic_layout_container layout_wall_feed');
      }

      <?php endif;?>

    });

  </script>
</div>




<div class="wallFeed feed pinfeed row-fluid" id="<?php echo $this->feed_uid?>" style="float: right; width: 100%;   margin: -12px 0;">


<?php

$tabs = Engine_Api::_()->wall()->getManifestType('wall_tabs');


// show only feed

if ($this->subject || !$this->viewer()->getIdentity()){

  $tab_disabled = array_diff(array_keys($tabs), array('social'));
  $tab_default = 'social';

  // show tabs

} else {

  $tab_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.disabled'));
  $tab_default = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.default', 'social');

}

?>




<div class="wall-streams">

  <?php if (!in_array('welcome', $tab_disabled)):?>
    <div class="wall-stream wall-stream-welcome <?php if ($tab_default == 'welcome'):?>is_active<?php endif;?>">
      <?php echo $this->render('_welcome.tpl');?>
    </div>
  <?php endif;?>
  <?php if ($this->enableComposer):?>
  <?php if (!in_array('social', $tab_disabled)):?>
  <div class="wall-stream wall-stream-social <?php if ($tab_default == 'social'):?>is_active<?php endif;?>">
    <ul class="wall-feed feed" id="activity-feed">
      <div class="pinfeed-main">
        <div id="header_hashtag" style="display: none"></div>
        <div class="pinfeeds">

          <!--     wall typs-->
          <li class="wall-stream-option wall-stream-option-social <?php if ($tab_default == 'social'):?>is_active<?php endif;?>" >

            <div class="wall-lists">
              <!--    <ul class="wall-types">
      <?php /*/*echo $this->render('_list.tpl'); */?>
          </ul>
-->
              <?php echo $this->partial('_activeList.tpl', 'pinfeed', array(
                'list_params' => $this->list_params,
                'types' => $this->types,
                'lists' => $this->lists,
                'friendlists' => $this->friendlists
              ))?>
              <ul class="wall-types">
                <?php echo $this->partial('_list.tpl', 'pinfeed', array(
                  'list_params' => $this->list_params,
                  'types' => $this->types,
                  'lists' => $this->lists,
                  'friendlists' => $this->friendlists
                ))?>
              </ul>
            </div>

          </li>
          <div style="float: left;">
            <div class="wallComposer wall-social-composer pinfeed_comments">

              <div class="wallFormComposer">
                <form method="post" action="<?php echo $this->url()?>">

                  <div class="wallComposerContainer">
                    <div class="wallTextareaContainer">
                      <div class="inputBox">
                        <div class="labelBox is_active">
                          <span><?php echo $this->translate('WALL_Post Something...');?></span>
                        </div>
                        <div class="textareaBox">
                          <div class="close"></div>
                          <textarea rows="1" cols="1" name="body"></textarea>
                          <input type="hidden" name="return_url" value="<?php echo $this->url() ?>" />
                          <?php if( $this->viewer() && $this->subject && !$this->viewer()->isSelf($this->subject)): ?>
                            <input type="hidden" name="subject" value="<?php echo $this->subject->getGuid() ?>" />
                          <?php endif; ?>
                        </div>
                      </div>
                      <div class="toolsBox"></div>

                    </div>
                  </div>

                  <div class="wall-compose-tray"></div>

                  <div class="submitMenu">
                      <?php  if(!$this->cat) { ?>
                        <select name="cat">
                                <option value="0">All</option>
                                <option value="1">Stocks</option>
                                <option value="2">Real Estate</option>
                                <option value="3">Retirement</option>
                                <option value="4">Other Savings</option>
                            </select>
                      <?php } ?>
                    <button type="submit" class="wall_composer_button">&nbsp;&nbsp;&nbsp;<?php echo $this->translate("WALL_Share") ?>&nbsp;&nbsp;&nbsp;</button>

                    <?php if ($this->allowPrivacy && count($this->privacy) > 1):?>

                      <div class="wall-privacy-container">
                        <a href="javascript:void(0);" class="wall-privacy-link wall_tips wall_blurlink" title="<?php echo $this->translate('WALL_PRIVACY_' . strtoupper($this->privacy_type) . '_'  . strtoupper($this->privacy_active));?>">
                          <span class="wall_privacy">&nbsp;</span>
                          <span class="wall_expand">&nbsp;</span>
                        </a>
                        <ul class="wall-privacy">
                          <?php foreach ($this->privacy as $item):?>
                            <li>
                              <a href="javascript:void(0);" class="item wall_blurlink <?php if ($item == $this->privacy_active):?>is_active<?php endif;?>" rev="<?php echo $item?>">
                                <span class="wall_icon_active">&nbsp;</span>
                                <span class="wall_text"><?php echo $this->translate('WALL_PRIVACY_' . strtoupper($this->privacy_type) . '_'  . strtoupper($item));?></span>
                              </a>
                            </li>
                          <?php endforeach ;?>
                        </ul>
                        <input type="hidden" name="privacy" value="<?php echo $this->privacy_active;?>" class="wall_privacy_input" />
                      </div>

                    <?php endif;?>

                    <ul class="wallShareMenu">
                      <?php

                      if ($this->viewer()->getIdentity()){

                        foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service){
                          $class = Engine_Api::_()->wall()->getServiceClass($service);
                          if (!$class || !$class->isActiveShare()) {
                            continue;
                          }
                          $a_class = 'wall-share-'.$service.' wall_tips disabled';

                          echo '<li class="service">
                        <a href="javascript:void(0);" class="'.$a_class.'" rev="'.$service.'" title="'.$this->translate('WALL_SHARE_' . strtoupper($service) . '').'"></a>
                        <input type="hidden" name="share['.$service.']" class="share_input" value="0"/>
                      </li>';

                        }
                      }
                      ?>
                    </ul>

                  </div>


                  <?php foreach( $this->composePartials as $partial ): ?>
                    <?php echo $this->partial($partial[0], $partial[1], array(
                      'feed_uid' => $this->feed_uid
                    )) ?>
                  <?php endforeach; ?>

                </form>
              </div>

            </div>
            <?php endif;?>


            <div class="pinfeed" id="pinfeed1" style="width: 280px; float: left">
                
            </div >
            <div class="pinfeed" id="pinfeed2" style="width: 280px; float: left">
            </div>
          </div>






          <!--     wall typs ends -->



          <div class="pinfeed" id="pinfeed3" style="width: 280px; float: left;margin-top: 60px; ">
          </div>
          <div class="pinfeed" id="pinfeed4" style="width: 280px; float: left;margin-top: 60px; ">
          </div>
          <div class="pinfeed" id="pinfeed5" style="width: 280px; float: left;margin-top: 60px; ">
          </div>
          <div class="pinfeed" id="pinfeed6" style="width: 280px; float: left;margin-top: 60px; ">
          </div>
          <div class="pinfeed" id="pinfeed7" style="width: 280px; float: left;margin-top: 60px; ">
          </div>
          <div style="clear: both;"></div>
          <div id = 'feed_block'></div>


          <?php if( $this->activity ): ?>
            <?php echo $this->wallActivityLoop($this->activity, array(
              'action_id' => $this->action_id,
              'viewAllComments' => $this->viewAllComments,
              'viewAllLikes' => $this->viewAllLikes,
              'comment_pagination' => $this->comment_pagination,
              'module' => 'pinfeed',
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
            <li class="wall-empty-feed">
              <div class="tip">
                <span>
                  <?php echo $this->translate("WALL_EMPTY_FEED") ?>
                </span>
              </div>
            </li>
          <?php endif; ?>

          <li class="utility-feed-config wall_displaynone" onclick='return(<?php echo Zend_Json::encode($this->feed_config)?>)'></li>

          <?php if ($this->firstid):?>
            <li class="utility-setlast" rev="item_<?php echo sprintf('%d', $this->firstid) ?>"></li>
          <?php endif;?>

    </ul>
  </div>
</div>
<?php endif;?>



<?php
/*if ($this->viewer()->getIdentity()){

  foreach (Engine_Api::_()->wall()->getManifestType('wall_service', true) as $service){

    if (in_array($service, $tab_disabled)){
      continue ;
    }

    $class = Engine_Api::_()->wall()->getServiceClass($service);

    if (!$class || !$class->isActiveStream()){
      continue ;
    }

    $tpl = $class->getFeedTpl();

    echo '<div class="wall-stream wall-stream-'.$service.'">
          <ul>
            <li class="wall-stream-tab-login wall-stream-tab">
              <div class="tip"><span>
              '.$this->translate('WALL_STREAM_'.strtoupper($service).'_LOGIN', array('<a href="javascript:void(0);" class="stream_login_link">', '</a>')).'
              </span></div>
            </li>
            <li class="wall-stream-tab-loader wall-stream-tab"><div class="wall-loader"></div></li>
            <li class="wall-stream-tab-stream wall-stream-tab">';

    echo $this->partial(@$tpl['path'], @$tpl['module'], array('feed_uid' => $this->feed_uid));

    echo "</li></ul></div>";

  }

}*/

?>

</div>

</div>
</div>
</div>
  <div class="video_background" style="display: none;"> </div>
<div id="videoViewer"></div>
<script type="text/javascript">

if($('activity-feed')) { column_count = Math.floor($('activity-feed').getComputedSize().width/275);}
else { column_count = 3;}
if(column_count<3){
  column_count = Math.floor($('activity-feed').getComputedSize().width/275);
  if(column_count<3){
    column_count = 3;
  }
}
  pinfeed_page = 1;
  start = 0;
  array = [];
  for (var i = 0; i < column_count; array[i++] = 0);
  //options.container.setStyle('width', column_count * 1);

  var options = {
    autoResize: true, // This will auto-update the layout when the browser window is resized.
    container: $('pinfeed'),
    item: $$('.wall-items-pinfeed'),
    offset: 2,
    itemWidth: 255,
    bottom: 0
  };
  var handler = $$('.wall-action-item');

  pinfeed(options);
  //}
</script>
