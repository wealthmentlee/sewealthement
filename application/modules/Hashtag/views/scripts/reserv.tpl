<?php
$hhh=1;
if($this->trand == 1){
  ?>
  <div class="hashtag_composer_cont display_none" style="margin-top: 10px;">
    <h3 class="h3_hashtag"> &nbsp;
      <?php
      if($this->trand == 1){
        $widget='widget';
      }else{
        $widget='';
        if($this->res_id>0){
          $res_id=$this->res_id;
        }
      }
      $url = $this->url(array('module' => 'hashtag', 'controller' => 'index', 'action' => 'search'), 'default', true);
      if($this->trand_plase>1){
        $name_prev = $this->trand_name[$this->trand_plase-1];
        echo '<a href="javascript:void(0)"  style="display: inline-block;position: relative; top: -3px;" onClick="click_hashtags(\'' . $url . '\',\'' . $name_prev . '\',\''.$this->translate("WALL_RECENT").'\',\''.$widget.'\',\''.($res_id-1).'\');" title="Hash tags, click for view more "  class="link_tag_sel" style="">';
        ?>

        <span class="left_hashtag"></span>

        </a>
      <?php
      }else{
        echo '<a href="javascript:void(0)" class="link_tag_sel" >';
        echo '<span class="left_hashtag_off" ></span>    </a>';
      }
      ?>

      <div style="    display: inline-block;    float: left;    font-size: 16px; " ><?php echo $this->translate('HASHTAG_TITLE');?></div>

      <div class="hastag_result" id="hastag_result">
        <?php echo  $this->name?>
      </div>
      <?php
      if($this->trand_plase<5){
        $name_next = $this->trand_name[$this->trand_plase+1];
        echo '<a href="javascript:void(0)"   onClick="click_hashtags(\'' . $url . '\',\'' . $name_next . '\',\''.$this->translate("WALL_RECENT").'\',\''.$widget.'\',\''.($res_id+1).'\');" title="Hash tags, click for view more "  class="link_tag_sel" style="">';
        ?>
        <span class="right_hashtag"></span>
        </a>
      <?php
      }else{
        echo '<a href="javascript:void(0)"   class="link_tag_sel" >';
        echo '<span class="right_hashtag_off" ></span></a>';
      }
      ?>
      <div id="c" class="count-o enebled">  <i></i><u></u> <a href="javascript:click_hashtags_closes('/test2/widget/index/name/wall.feed');" rev="recent" class="wp_init"> <span class="hashtag_close"></span></a>
      </div>

    </h3>
    <script type="text/javascript">
      document.addEventListener('DOMContentLoaded', function() {
        var i = $("hastag_result");
        var o = $("hashtag_h3");
        i.css("marginLeft", (o.width() - i.width()) / 2);
        i.css("marginTop", (o.height() -i.height()) / 2);

      }, false);
    </script>
  </div>
<?php
}else{

  ?>
  <div class="hashtag_composer_cont display_none" style="margin-top: 10px;">
    <h3  class="h3_hashtag"> &nbsp;
      <?php

      $widget='';
      if($this->res_id>0){
        $res_id=$this->res_id;

      }
      $url = $this->url(array('module' => 'hashtag', 'controller' => 'index', 'action' => 'search'), 'default', true);
      if($this->trand_plase>1){
        $name_prev = $this->trand_name[$this->trand_plase-1];
        $res_prev = $this->res_name[$this->trand_plase-1];
        echo '<a href="javascript:void(0)"  style="display: inline-block;position: relative; top: -3px;" onClick="click_hashtags(\'' . $url . '\',\'' . $name_prev . '\',\''.$this->translate("WALL_RECENT").'\',\''.$widget.'\',\''.$res_prev.'\');" title="Hash tags, click for view more "  class="link_tag_sel" style="">';
        ?>
        <span class="left_hashtag" style=""></span>
        </a>
      <?php
      }else{
        echo '<a href="javascript:void(0)"   class="link_tag_sel" >';
        echo '<span class="left_hashtag_off" ></span></a>';
      }
      ?>

      <div style=" display: inline-block;    float: left;    font-size: 16px; " ><?php echo $this->translate('HASHTAG_TITLE');?></div>

      <div class="hastag_result" id="hastag_result">
        <?php echo  $this->name?>
      </div>
      <?php
      if($this->trand_plase < $this->count){
        $name_next = $this->trand_name[$this->trand_plase+1];
        $res_next = $this->res_name[$this->trand_plase+1];
        echo '<a href="javascript:void(0)"    onClick="click_hashtags(\'' . $url . '\',\'' . $name_next . '\',\''.$this->translate("WALL_RECENT").'\',\''.$widget.'\',\''.$res_next.'\');" title="Hash tags, click for view more "  class="link_tag_sel" style="">';
        ?>
        <span class="right_hashtag"></span>
        </a>
      <?php
      }else{
        echo '<a href="javascript:void(0)"   class="link_tag_sel" >';
        echo '<span class="right_hashtag_off" ></span></a>';
      }
      ?>
      <div id="c" class="count-o enebled">  <i></i><u></u> <a href="javascript:click_hashtags_closes('/test2/widget/index/name/wall.feed');" rev="recent" class="wp_init"> <span class="hashtag_close"></span></a>
      </div>

    </h3>
    <script type="text/javascript">
      document.addEventListener('DOMContentLoaded', function() {
        var i = $("hastag_result");
        var o = $("hashtag_h3");
        i.css("marginLeft", (o.width() - i.width()) / 2);
        i.css("marginTop", (o.height() -i.height()) / 2);

      }, false);
    </script>
  </div>







<?php
}

?>
 