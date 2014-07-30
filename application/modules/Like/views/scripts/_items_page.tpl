<div class="most-liked-<?php echo $this->item_type?>s">
  <div class="list">
    <?php foreach($this->likes as $item_like): ?>
      <?php if(null!=$item = Engine_Api::_()->getItem($item_like['resource_type'],$item_like['resource_id'])): ?>
      <div class="item">
        <?php
          if($item_like['resource_type'] == 'blog')
          {
            echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getOwner(),'thumb.icon'), array('class'=>'most-liked-'.$item_like['resource_type'].' display_block', 'id' => 'most_liked_'.$item_like['resource_type']. '_'.$item->getIdentity()));
          }
          elseif($item_like['resource_type'] == 'pageblog')
          {
            echo $this->htmlLink($item->getHref(), $this->itemPhoto($item->getPage(),'thumb.icon'), array('class'=>'most-liked-'.$item_like['resource_type'].' display_block', 'id' => 'most_liked_'.$item_like['resource_type']. '_'.$item->getIdentity()));
          }
          else
          {
            echo $this->htmlLink($item->getHref(), $this->itemPhoto($item,'thumb.icon'), array('class'=>'most-liked-'.$item_like['resource_type'].' display_block', 'id' => 'most_liked_'.$item_like['resource_type']. '_'.$item->getIdentity()));
          }
        ?>
        <div title="<?php echo $this->translate(array('like_%s like', 'like_%s likes',$item_like['like_count']),$item_like['like_count']); ?>" class="like_count"><?php echo $item_like['like_count']; ?></div>
      </div>
      <?php endif; ?>
    <?php endforeach; ?>
    <div class="clr"></div>
  </div>
</div>