<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _item.tpl 2011-03-28 17:14 taalay $
 * @author     Taalay
 */
?>

<?php if ($this->widget == 'most_liked') : ?>
  <?php  $total_items = count($this->like_items); ?>

  <?php if ($total_items == 0) : ?>

    <div class="he_like_no_content"><?php echo $this->translate('There are no content.'); ?></div>

  <?php else : ?>

  <div class="most-liked-<?php echo $this->item_type?>s">
    <div class="list">
      <?php foreach($this->counts as $item_id => $count):
			 if ( null != ($item = Engine_Api::_()->getItem($this->item_type, $item_id))): ?>
      <div class="item">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'most-liked-' . $this->item_type . ' display_block', 'id' => 'most_liked_' . $this->item_type . '_'.$item->getIdentity())); ?>
        <div title="<?php echo $this->translate(array('like_%s like', 'like_%s likes', $this->counts[$item->getIdentity()]), $this->counts[$item->getIdentity()]); ?>" class="like_count"><?php echo $count; ?></div>
      </div>
			 <?php endif; ?>
      <?php endforeach; ?>
      <div class="clr"></div>
    </div>
  </div>

  <?php endif; ?>
<?php elseif ($this->widget == 'profile_likes') : ?>
  <?php
    $total_items = $this->total;
  ?>

  <?php if ($total_items == 0) : ?>
    <div class="he_like_no_content"><?php echo $this->translate('There are no content.'); ?></div>
  <?php else : ?>
  <div class="see_all_container" style="margin-left: 12px;">
    <?php
      $label = $this->translate(array("like_%s item", "like_%s items", $this->total), ($this->total));
      echo ($this->total && $this->likedMembersAndPages)
        ? $this->htmlLink("javascript:like.see_all_liked({$this->subject->getIdentity()}, '$this->period_type');", $label)
        : $this->htmlLink($this->url(array('action' => 'index', 'user_id' => $this->subject->getIdentity(), 'period_type' => $this->period_type), 'like_default'), $label, array('target' => '_blank'));
      ?>
  </div>

  <?php
    $count = 0;
    $nophoto_items = array('blog', 'pageblog', 'poll', 'classified');
  ?>
  <div class="clr"></div>
  <div class="list">
    <?php if (!empty($this->items)): ?>
      <?php $counter = 0; ?>
      <?php foreach ($this->items as $like): ?>
        <?php
          if (!($like instanceof Core_Model_Item_Abstract)){
	    continue ;
	  }
          if ($count >= $this->ipp) {
            break;
          }
          $count++;
        ?>
        <div class="item">
          <?php
            if ( in_array( $like->getType(), $nophoto_items ) ){
              $photo = $this->htmlImage($this->baseUrl() . '/application/modules/Like/externals/images/nophoto/' . $like->getType() . '.png', '', array(
                'class' => 'thumb_icon item_photo_' . $like->getType()
              ));
            }else{
              $photo = $this->itemPhoto($like, 'thumb.icon');
            }
          ?>
          <?php echo $this->htmlLink($like->getHref(), $photo, array('class' => 'like_profile_tip')); ?>
          <div class="like_tip_title hidden"></div>
          <div class="like_tip_text hidden"><?php echo $like->getTitle(); ?></div>
        </div>
        <?php $counter++; ?>
        <?php if ($counter % 3 == 0): ?><div class="clr"></div><?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
    <div class="clr"></div>
  </div>

  <div class="clr" style="margin-bottom:10px;"></div>
  <?php endif; ?>


<?php elseif ($this->widget == 'box') : ?>
  <?php
    $total_items = $this->likes->getTotalItemCount();
  ?>
  <?php if ($total_items == 0) : ?>
    <div class="he_like_no_content"><?php echo $this->translate('There are no content.'); ?></div>
  <?php else : ?>
      <div class="see_all_container" style="margin-left: 12px;">
        <a href="javascript:like.see_all('<?php echo $this->subject->getType(); ?>', <?php echo $this->subject->getIdentity(); ?>, '<?php echo $this->period_type; ?>');">
          <?php echo $this->translate(array("like_%s like", "like_%s likes", $this->likes->getTotalItemCount()), ($this->likes->getTotalItemCount())); ?>
        </a>
      </div>

      <div class="clr"></div>
      <div class="list">
        <?php $counter = 0; ?>
        <?php foreach ($this->likes as $like): ?>
          <div class="item">
            <a class="like_tool_tip_links" href="<?php echo $like->getHref(); ?>">
              <?php echo $this->itemPhoto($like, 'thumb.icon'); ?>
            </a>
            <div class="like_tip_title hidden"></div>
            <div class="like_tip_text hidden"><?php echo $like->getTitle(); ?></div>
          </div>
          <?php $counter++; ?>
          <?php if ($counter % 3 == 0): ?><div class="clr"></div><?php endif; ?>
        <?php endforeach; ?>
        <div class="clr"></div>
      </div>
    <div class="clr" style="margin-bottom:10px;"></div>
  <?php endif; ?>
<?php endif; ?>