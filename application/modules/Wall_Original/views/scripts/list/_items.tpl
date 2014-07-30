<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _items.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php if ($this->items):?>
  
  <?php foreach ($this->items as $item):?>

    <li class="item item_<?php echo $item->getGuid()?>" rev="item_<?php echo $item->getGuid()?>">

      <div class="item_photo" rev="<?php echo $item->getGuid()?>">
        <?php echo $this->itemPhoto($item, 'thumb.icon')?>
        <div class="inner"></div>
      </div>

      <div class="item_body">

        <div class="item_title">
          <?php echo Engine_String::substr($item->getTitle(), 0, 100)?>
        </div>

      </div>

    </li>

  <?php endforeach ; ?>

<?php endif;?>

