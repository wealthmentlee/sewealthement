<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: item.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<div class="suggest-object">
  <?php if (!in_array($this->object->getType(), array('blog'))): ?>
    <div class="photo">
      <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->itemPhoto($this->object, $this->thumb, '', array('style' => 'max-height: 110px'))); ?>
    </div>
  <?php else: ?>
    <div class="photo">
      <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->htmlImage($this->baseUrl().'/application/modules/Suggest/externals/images/nophoto/blog.png', '', array('style' => 'max-height: 110px'))); ?>
    </div>
  <?php endif; ?>
  <div class="info">
    <div class="title">
      <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->object->getTitle()); ?>
      <?php echo $this->likeEnabled ? $this->likeButton($this->object) : ''; ?>
    </div>
    <div class="clr"></div>
    <div class="description">
      <?php echo $this->suggest->getDescription(); ?>
    </div>
    <div class="clr"></div>
    <?php echo $this->suggestOptions($this->suggest); ?>
  </div>
  <div class="clr"></div>
</div>