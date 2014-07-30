<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div class="clear">
  <div class="settings">
    <h3><?php echo $this->translate('AS_Advanced Search Configuration')?></h3>
    <form method="post">
      <div>
        <div>
          <p>
            <?php echo $this->translate('AS_Add items which will be included in search');?>
          </p>
          <?php if ($this->formSaved):?>
            <ul class="form-notices">
              <li><?php echo $this->translate($this->formSaved)?></li>
            </ul>
          <?php endif;?>
          <div style="margin: 10px 5px">
            <input type="checkbox" id="all_types_select"><span class="item_type_select" style="cursor: pointer"><?php echo $this->translate('Select/Deselect All')?></span>
            <?php foreach ($this->types as $type):?>
              <div style="margin: 5px">
                <input class="as_types_list" <?php if (in_array($type, $this->viewList)):?>checked="checked" <?php endif;?>  name="types[]" type="checkbox" value="<?php echo $type;?>"><span class="item_type_select" style="cursor: pointer"><?php echo $this->translate('ITEM_TYPE_'.strtoupper($type));?></span><br>
              </div>
            <?php endforeach?>
          </div>
          <button type="submit"><?php echo $this->translate('AS_Save changes');?></button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
  $$('.item_type_select').addEvent('click', function(){
    if ($(this).getPrevious().get('checked')) {
      $(this).getPrevious().set('checked', '')
      $(this).getPrevious().fireEvent('change');
    } else {
      $(this).getPrevious().set('checked', 'checked')
      $(this).getPrevious().fireEvent('change');
    }
  });
  $('all_types_select').addEvent('change', function(){
    if ($(this).get('checked')) {
      $$('.as_types_list').set('checked', 'checked');
    } else {
      $$('.as_types_list').set('checked', '');
    }
  });
</script>