<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>


<?php
echo $this->render('_header.tpl');
?>


<div class="wall_gift_actual">
  <ul>
    <?php

    foreach ($this->items as $item):

?>
        <li>
          <div class="item_photo">
            <a href="<?php echo $item->getHref();?>"><?php echo $this->itemPhoto($item, 'thumb.profile');?></a>
          </div>
          <div class="item_body">
            <div class="item_title">
              <a href="<?php echo $item->getHref()?>"><?php echo $item->getTitle();?></a>
            </div>
            <div class="item_description">
              <?php if ($item->credits) : ?>
                <?php echo $this->translate("HEGIFT_%s credit", $this->locale()->toNumber($item->credits))?>
              <?php else : ?>
                <?php echo $this->translate('HEGIFT_Free')?>
              <?php endif; ?>
            </div>
          </div>
          <div class="item_options">
            <a href="<?php echo $item->getHref();?>" class="buttonlink birthday_widget_link item_icon_gift"><?php echo $this->translate('HEGIFT_Send Gift');?></a>
          </div>
        </li>

      <?php endforeach ;?>
  </ul>
</div>