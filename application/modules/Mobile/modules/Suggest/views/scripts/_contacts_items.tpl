<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _contacts_items.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<?php if (!empty($this->potentialItems) && $this->potentialItems->getCurrentItemCount() > 0): ?>
<div class="recommended-title"><?php echo $this->translate("Recommended To Suggest"); ?></div>
<div class="clr"></div>
<ul class="recommended items">
  <?php foreach ($this->potentialItems as $item): ?>
  <?php $itemDisabled = in_array($item->getIdentity(), $this->disabledItems); ?>
  <?php $itemChecked = in_array($item->getIdentity(), $this->checkedItems); ?>
  <li>
    <div class='check'>
      <?php if (!$itemDisabled): ?>
      <input type="checkbox" value="<?php echo $item->getIdentity(); ?>" name="contacts[]"/>
      <?php else: ?>
      
      <?php endif; ?>
    </div>

    <div class='item_photo'>
      <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')); ?>
    </div>

    <div class="item_body">
      <div class="item_title">
        <?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?>
      </div>
    </div>
    <div class="clr"></div>
  </li>
  <?php endforeach; ?>
</ul>
<div class="clr"></div>

<?php endif; ?>

<?php if (!empty($this->items) && $this->items->getCurrentItemCount() > 0): ?>
<h4 class="all-title">&raquo; <?php echo $this->translate("Friends"); ?></h4>
<div class="clr"></div>

<ul class="all items">
  <?php foreach ($this->items as $item): ?>
  <?php $itemDisabled = in_array($item->getIdentity(), $this->disabledItems); ?>
  <?php $itemChecked = in_array($item->getIdentity(), $this->checkedItems); ?>
  <li>
    <div class='check'>
      <?php if (!$itemDisabled): ?>
      <input type="checkbox" value="<?php echo $item->getIdentity(); ?>" name="contacts[]"/>
      <?php else: ?>
      
      <?php endif; ?>
    </div>

    <div class='item_photo'>
      <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')); ?>
    </div>

    <div class="item_body">
      <div class="item_title">
        <?php echo $this->htmlLink($item->getHref(), $item->getTitle()); ?>
      </div>
    </div>

    <div class="clr"></div>
  </li>
  <?php endforeach; ?>
  <div class="clr"></div>
</ul>
<?php endif; ?>