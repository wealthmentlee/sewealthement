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

<ul class="profile_info">
  <?php if( !empty($this->memberType) ): ?>
  <li>
    <span class="type"><?php echo $this->translate('Member Type:') ?></span>
    <?php // @todo implement link ?>
    <span class="value"><?php echo $this->translate($this->memberType) ?></span>
  </li>
  <?php endif; ?>
  <?php if( !empty($this->networks) && count($this->networks) > 0 ): ?>
  <li>
    <span class="type"><?php echo $this->translate('Networks:') ?></span>
    <span class="value"><?php echo $this->fluentList($this->networks) ?></span>
  </li>
  <?php endif; ?>
  <li>
    <span class="type"><?php echo $this->translate('Profile Views:') ?></span>
    <span class="value"><?php echo $this->translate(array('%s view', '%s views', $this->subject()->view_count),$this->locale()->toNumber($this->subject()->view_count)) ?></span>
  </li>
  <li>
    <span class="type"><?php echo $this->translate('Friends:') ?></span>
    <span class="value"><?php echo $this->translate(array('%s friend', '%s friends', $this->subject()->member_count),$this->locale()->toNumber($this->subject()->member_count)) ?></span>
  </li>
  <li>
    <span class="type"><?php echo $this->translate('Last Update:'); ?></span>
    <span class="value"><?php echo $this->timestamp($this->subject()->modified_date) ?></span>
  </li>
  <li>
    <span class="type"><?php echo $this->translate('Joined:') ?></span>
    <span class="value"><?php echo $this->timestamp($this->subject()->creation_date) ?></span>
  </li>
</ul>