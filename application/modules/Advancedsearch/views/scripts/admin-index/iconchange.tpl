<div class="settings">
  <form method="post">
    <div>
        <?php foreach ($this->icons as $icon):?>
          <div class="icons_list" style="font-size: 22px;margin: 3px;float: left;cursor: pointer">
            <i class="<?php echo $icon;?>"></i>
          </div>
        <?php endforeach;?>
    </div>
    <input type="hidden" name="icon" value="icon-globe" id="item_icon">
    <span style="font-size: 22px;margin: 10px 0">
      <?php echo $this->translate('AS_icon')?>: <i id="icon" class="<?php if (isset($this->item['icon'])) echo $this->item['icon']; else echo 'icon-globe';?>"></i>
    </span><br>
    <button type="submit">
      <?php echo $this->translate('AS_Save changes');?>
    </button>
  </form>
</div>
<script>
  $$('.icons_list').addEvent('click', function(){
    var icon = $(this).getChildren('i').get('class');
    $('icon').set('class', icon);
    $('item_icon').set('value', icon);
  });
</script>