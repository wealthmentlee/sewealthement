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



<h3> Hash Tags <div style="float: right;" > <span class="hastag_result"><?php echo $this->name; ?></span>
    <span class="count-o enebled" id="c">  <i></i><u></u> <a  rev="recent" href="javascript:click_hashtags_close('<?php echo $url; ?>');"> <span class="hashtag_close"></span></a></span>
  </div></h3>
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



    <?php endif;?>

    <?php else:?>

    var tab_link = $$('.tab_layout_wall_feed')[0];
    if (tab_link && tabContainerSwitch){
      tabContainerSwitch(tab_link, 'generic_layout_container layout_wall_feed');
    }

    <?php endif;?>

  });

</script>

<div class="wallFeed" id="<?php echo $this->feed_uid?>">


  <?php

  $tabs = Engine_Api::_()->wall()->getManifestType('wall_tabs');


  // show only feed


  $tab_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.disabled'));
  $tab_default = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.default', 'social');



  ?>




  <div class="wall-streams">

    <?php if (!in_array('welcome', $tab_disabled)):?>
      <div class="wall-stream wall-stream-welcome <?php if ($tab_default == 'welcome'):?>is_active<?php endif;?>">
        <?php echo $this->render('_welcome.tpl');?>
      </div>
    <?php endif;?>

    <?php if (!in_array('social', $tab_disabled)):?>
      <div class="wall-stream wall-stream-social <?php if ($tab_default == 'social'):?>is_active<?php endif;?>">



        <ul class="wall-feed feed" style="padding: 0px">
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



        echo "</li></ul></div>";

      }

    }
    ?>

  </div>

</div>
