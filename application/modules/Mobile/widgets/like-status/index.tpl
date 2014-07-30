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

<div id='profile_status'>
  
  <h2 class="like_status_header">
    <?php echo $this->subject()->getTitle() ?>
  </h2>

  <?php if ($this->module_enabled):?>

    <?php if ($this->is_enabled) { ?>
      <?php
      if (!$this->is_liked) {
        $url = $this->url(array('action' => 'like', 'object_type' => $this->subject()->getType(), 'object_id' => $this->subject()->getIdentity()), 'like_default');
        $label = 'like_Like';
      } else {
        $url = $this->url(array('action' => 'unlike', 'object_type' => $this->subject()->getType(), 'object_id' => $this->subject()->getIdentity()), 'like_default');
        $label = 'like_Unlike';
      }
      ?>
      <div class="like_button_container">

        <form action="<?php echo $url; ?>">
          <button type="submit" class="like_button"><?php echo $this->translate($label); ?></button>
          <input type="hidden" name="return_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
        </form>
        <div class="clr"></div>

      </div>
    <?php } ?>
    <div class="clr"></div>

  <?php endif;?>
  
</div>