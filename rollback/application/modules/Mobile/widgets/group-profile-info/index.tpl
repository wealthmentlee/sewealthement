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

<ul class="items_details">
  <li class="group_stats_title">
    <span>
      <?php echo $this->group->getTitle() ?>
    </span>
    <?php if( !empty($this->group->category_id) ): ?>
      <?php echo $this->translate($this->group->getCategory()->title)?>
    <?php endif; ?>
  </li>
  <?php if( '' !== ($description = $this->group->description) ): ?>
    <li class="group_stats_description">
      <?php echo $this->mobileSubstr($description) ?>
    </li>
  <?php endif; ?>
  <li class="group_stats_staff">
    <ul>
      <?php foreach( $this->staff as $info ): ?>
        <li>
          <?php echo $info['user']->__toString() ?>
          <?php if( $this->group->isOwner($info['user']) ): ?>
            (<?php echo ( !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : $this->translate('owner') ) ?>)
          <?php else: ?>
            (<?php echo ( !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : $this->translate('officer') ) ?>)
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </li>
  <li class="group_stats_info">
    <ul>
      <li><?php echo $this->translate(array('%s total view', '%s total views', $this->group->view_count), $this->locale()->toNumber($this->group->view_count)) ?></li>
      <li><?php echo $this->translate(array('%s total member', '%s total members', $this->group->member_count), $this->locale()->toNumber($this->group->member_count)) ?></li>
      <li><?php echo $this->translate('Last updated %s', $this->timestamp($this->group->modified_date)) ?></li>
    </ul>
  </li>
</ul>