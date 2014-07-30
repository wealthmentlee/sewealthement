<?php if (count($this->navigation)): ?>
  <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>
<div style="margin: 10px 0 10px">
  <?php echo $this->translate('AS_Configure your items icons')?>
</div>
<table class="admin_table">
  <thead>
  <tr>
    <th class="admin_table_short"><?php echo $this->translate('AS_item')?></th>
    <th class="admin_table_short"><?php echo $this->translate('AS_icon')?></th>
  </tr>
  </thead>
  <?php foreach ($this->types as $type): ?>
    <tr>
      <td>
        <?php echo $this->translate('ITEM_TYPE_' . strtoupper($type))?>
      </td>
      <td>
        <i style="font-size: 22px" class="<?php if(isset($this->itemicons[$type])) echo $this->itemicons[$type]; else echo 'icon-globe';?>"></i>|<a
        <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'advancedsearch', 'controller' => 'index', 'action' => 'iconchange', 'item' => $type),
          $this->translate('AS_change'),
          array('class' => 'smoothbox')) ?>
      </td>
    </tr>
  <?php endforeach;?>
</table>