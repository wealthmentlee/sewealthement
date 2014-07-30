
<?php
  $url = $this->url(array('module' => 'hashtag','controller' => 'index','action'=>'search' ), 'default', true);
  foreach($this->tags as $tag):
    if($this->active_pin == 1 && $this->enebled_pin){
?>
      <div style="margin: 5px; padding: 5px;">
        <?php echo '<a href="javascript:void(0)" class="trands_hashtag" onClick="click_hashtags(\''.$url.'\',\''.$tag->hashtag. '\', \''.$this->translate("WALL_RECENT").'\',\'widget\',\'\',true);" title="'.$this->translate('TITLE_LINK_HASHTAG').'">#'.$tag->hashtag.'</a>'; ?>
      </div><div style="clear: both"></div>
      <?
    }else{
    ?>

    <?php
    }

    ?>

<?php endforeach; ?>

