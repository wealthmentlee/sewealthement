<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: photo.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<div class="suggest-object">
  <div class="photo">
    <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->itemPhoto($this->object, null, '', array('height' => '110px'))); ?>
  </div>

  <div class="info">
    <div class="description">
      <?php echo $this->suggest->getDescription(); ?>
    </div>
    <div class="clr"></div>
    <?php echo $this->suggestOptions($this->suggest); ?>
  </div>
  
  <div class="clr"></div>
</div>