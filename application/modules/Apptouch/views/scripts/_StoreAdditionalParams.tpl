
<?php
  $values = $this->element->getValue();
  $i = 0;

  if (count($values) == 0){
    $values[] = '';
  }
?>

<p>
  <div id='additional-params'>
  <?php foreach($values as $key=>$value): ?>
    <div id="param-block-<?php echo $key;?>" class="param-block">
      <div class='title-block'>
        <span><?php echo $this->translate('Label'); ?></span><br/>
        <input type='text' name='additional_params[]' class="param-title" value="<?php echo (isset($value['label']))?$value['label']:''; ?>"/>
      </div>
      <div>
        <span><?php echo $this->translate('STORE_Options. Comma separated.'); ?></span><br/>
        <input type='text' name='additional_params[options][]' class="param-options" value="<?php echo (isset($value['options']))?$value['options']:''; ?>"/>
      </div>
      <?php if ($i > 0 ): ?>
        <a delete-id="<?php echo $key?>" class="param-delete" href="javascript:void(0);">X</a>
      <?php endif; ?>
    </div>
    <?php $i++; endforeach; ?>
  </div>
  <a id="add-more" next="<?php echo ($key + 1);?>" href="javascript:void(0);"><?php echo $this->translate('add more'); ?></a>
</p>