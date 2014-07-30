<?php $i=0;?>
<?php foreach( $this->items as $item ):
  $item = $this->item($item->type, $item->id);
 ?>
  <div class="as_search_result">
    <div class="search_photo">
      <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
    </div>
    <div class="search_info">
      <?php if( '' != $this->query ): ?>
        <?php echo $this->htmlLink($item->getHref(), $this->highlightText($item->getTitle(), $this->query), array('class' => 'search_title')) ?>
      <?php else: ?>
        <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'search_title')) ?>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
<?php //print_die($i);?>