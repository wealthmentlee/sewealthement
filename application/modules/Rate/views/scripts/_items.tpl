<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _album_photos.tpl 2011-03-16 16:14 ermek $
 * @author     Ermek
 */

?>

<?php
	$total_items = count($this->rate_items);
  $counter = 1;
?>

<?php if ($total_items == 0) : ?>

  <div class="he_rate_no_content"><?php echo $this->translate('There are no content.'); ?></div>

<?php else : ?>
  
  <?php foreach ($this->rate_items as $key => $item) : ?>
    <?php if(isset($this->items[$item['object_id']]) && $this->items[$item['object_id']] !== null) : ?>
      <div class="<?php echo ($counter != $total_items) ? 'he_rate_item' : 'he_rate_item_last'; ?>">
        <div class="he_rate_thumb"><?php echo $this->itemPhoto( $this->items[$item['object_id']], 'thumb.icon'); ?></div>
        <div class="he_rate_body">
          <div class="he_rate_title">
	          <?php echo $this->htmlLink( $this->items[$item['object_id']]->getHref(), $this->string()->truncate( $this->items[$item['object_id']]->getTitle(), 15, '...')) ?>
          </div>
          <?php echo $this->itemRate($this->item_type, $item['object_id'], true, true, $this->period); ?>
        </div>
        <div class="clr"></div>
      </div>
      <?php $counter++ ?>
    <?php endif; ?>
  <?php endforeach; ?>
	<?php if ($counter === 1) : ?>
		<div class="he_rate_no_content"><?php echo $this->translate('There are no content.'); ?></div>
	<?php endif; ?>
<?php endif; ?>