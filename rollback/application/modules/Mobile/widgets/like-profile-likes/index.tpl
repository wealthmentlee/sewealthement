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

<?php
  $nophoto_items = array('blog', 'pageblog', 'poll', 'classified');
  $counter = 0;
?>
<ul class='items'>

  <?php foreach ($this->items as $like): ?>

  <li>
    <div class="item_photo">
      <?php
                if (in_array($like->getType(), $nophoto_items)) {
      $photo = $this->htmlImage($this->baseUrl() . '/application/modules/Like/externals/images/nophoto/' . $like->getType() . '.png', '', array(
        'class' => 'thumb_icon item_photo_' . $like->getType()
      ));
    } else {
      $photo = $this->itemPhoto($like, 'thumb.icon');
    }
      ?>
      <?php echo $this->htmlLink($like->getHref(), $photo, array('class' => 'profile_friends_icon')) ?>
    </div>

    <div class='item_body'>
      <div class='item_title'>
        <?php echo $this->htmlLink($like->getHref(), $like->getTitle()); ?>
      </div>
    </div>
  </li>
  
  <?php $counter++; ?>
  
  <?php if ($counter >= $this->ipp): ?>
    <?php break; ?>
  <?php endif; ?>
  
  <?php endforeach;?>
</ul>