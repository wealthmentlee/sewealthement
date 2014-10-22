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

  <ul class="thumbs">
  <?php foreach ($this->rate_items as $key => $item) { ?>

    <li class="<?php echo ($counter != $total_items) ? 'he_rate_item' : 'he_rate_item_last'; ?>">
      <div class="he_rate_title"><?php echo $this->htmlLink($this->items[$item['object_id']]->getHref(), $this->items[$item['object_id']]->getTitle()) ?></div>
      <?php echo $this->itemRate('blog', $item['object_id'], true, false); ?>
    </li>
  
    <?php $counter++ ?>
  <?php } ?>
  </ul>

<?php endif; ?>