<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

?>

<?php if (count($this->admins)):?>

<ul class="items">

  <?php foreach ($this->admins as $admin): ?>
    <li>
      <div class="item_photo">
        <?php echo $this->itemPhoto($admin, 'thumb.icon')?>
      </div>
      <div class="item_body">
        <div class="item_title"><?php echo $admin->__toString()?></div>
      </div>
    </li>

  <?php endforeach;?>

</ul>

<?php endif;?>