<?php if ($this->global): ?>
  <?php foreach ($this->items as $item):
    $type = $item['type'];
    if (!Engine_Api::_()->hasItemType($type)) {
      continue;
    }
    $item = $this->item($item['type'], $item['id']);
    if (!$item) continue; ?>
    <a style="display: block" href="<?php echo $item->getHref() ?>">
      <div class="as_global_search_result search_result">
        <div class="as_global_search_photo">
          <?php if ($item->getPhotoUrl() != ''): ?>
            <?php echo $this->itemPhoto($item, 'thumb.icon') ?>
          <?php else: ?>
            <img
              src="<?php echo $this->layout()->staticBaseUrl . 'application/modules/Advancedsearch/externals/images/nophoto_icon.png' ?>">
          <?php endif;?>
        </div>
        <div class="as_global_search_info">
          <span><?php echo $this->highlightText($item->getTitle(), $this->query) ?></span>
          <span style="color: #999999"><?php echo $this->translate('ITEM_TYPE_' . strtoupper($type))?></span>
          <p style="margin-left: 15px">
            <?php if ($type == 'page') {
              echo $item->getLikesCount() . $this->translate('AS_likes');
            } elseif ($type == 'album' || $type == 'pagealbum') {
              echo $item->count() . $this->translate('AS_photos');
            }  elseif ($type == 'video' || $type == 'pagevideo') {
              echo $item->view_count . $this->translate('AS_views');
            } elseif ($type == 'group' || $type == 'event' || $type == 'pageevent') {
              echo $item->member_count . $this->translate('AS_members');
            }
            ?>
          </p>
        </div>
      </div>
      <div style="clear:left"></div>
    </a>
  <?php endforeach; ?>
  <?php if (count($this->items) == 5):?>
    <a class="as_global_found_more_link" href="<?php echo $this->url(array('squery' => $this->query, 'stype' => $this->stype), 'advancedsearch')?>">
      <div class="as_global_found_more">
          <?php echo $this->translate('AS_more');?>
      </div>
    </a>
  <?php endif;?>
<?php else:?>
  <?php foreach ($this->items as $item):
    $type = $item->type;
    if (!Engine_Api::_()->hasItemType($item->type)) {
      continue;
    }
    $checkItem = $item;
    $item = $this->item($item->type, $item->id);
    if (!$item) {
      Engine_Api::_()->advancedsearch()->deleteItem($checkItem->type, $checkItem->id);
      continue;
    } ?>
    <div class="as_search_result">
      <div class="as_search_photo">
<!--          --><?php //echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
        <a href="<?php echo $item->getHref(); ?>">
          <?php if ($item->getPhotoUrl() != ''): ?>
            <span style="width:200px; height: 200px;background-image: url(<?php echo $item->getPhotoUrl($this->imageTypes($type))?>);background-position: center 50%;display: block;background-repeat: no-repeat"></span>
          <?php else:?>
            <span style="width:200px; height: 200px;background-image: url(<?php echo $this->layout()->staticBaseUrl . 'application/modules/Advancedsearch/externals/images/nophoto.png'?>);background-position: center 50%;display: block;"></span>
          <?php endif;?>
        </a>
      </div>
      <div class="as_search_info">
        <?php if ('' != $this->query): ?>
          <?php echo $this->htmlLink($item->getHref(), $this->highlightText($item->getTitle(), $this->query), array('class' => 'search_title')) ?>
        <?php else: ?>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'search_title')) ?>
        <?php endif; ?>
        <p>
          <?php echo $this->translate('ITEM_TYPE_' . strtoupper($type))?>
          <?php if ($type == 'page') {
            echo ' <b>&#149;</b> '.$item->getLikesCount() . $this->translate('AS_likes');
          } elseif ($type == 'album' || $type == 'pagealbum') {
             echo ' <b>&#149;</b> '.$item->count() . $this->translate('AS_photos');
          }  elseif ($type == 'video' || $type == 'pagevideo') {
             echo ' <b>&#149;</b> '.$item->view_count . $this->translate('AS_views');
          } elseif ($type == 'group' || $type == 'event' || $type == 'pageevent') {
            echo ' <b>&#149;</b> '.$item->member_count . $this->translate('AS_members');
          }
          ?>
        </p>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>