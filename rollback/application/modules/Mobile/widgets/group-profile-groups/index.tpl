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
<ul class="items";>
  <?php foreach( $this->paginator as $group ): ?>
    <li>
      <div class="item_photo">
        <?php echo $this->htmlLink($group, $this->itemPhoto($group, 'thumb.normal')) ?>
      </div>
      <div class="item_body">
        <div class="item_title">
          <?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?>
        </div>
        <div class="item_date">
          <?php echo $this->translate(array('%s member', '%s members', $group->member_count),$this->locale()->toNumber($group->member_count)) ?>
        </div>
        <div class="item_desc">
          <?php echo $this->mobileSubstr($group->getDescription()); ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<div>
<?php echo $this->htmlLink(
  $this->url(array('user' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'group_general'),
  $this->translate('View Groups'),
  array('class' => 'buttonlink item_icon_event')) ?>
</div>