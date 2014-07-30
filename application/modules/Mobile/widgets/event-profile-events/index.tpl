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
<div class="layout_content">
<ul class="items">
  <?php foreach( $this->paginator as $event ): ?>
    <li>
      <div class="item_photo">
        <?php echo $this->htmlLink($event, $this->itemPhoto($event, 'thumb.normal')) ?>
      </div>
      <div class="item_body">
        <div>
          <?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
        </div>
        <div>
          <?php echo $this->translate(array('%s guest', '%s guests', $event->member_count),$this->locale()->toNumber($event->member_count)) ?>
        </div>
        <div>
          <?php echo $this->mobileSubstr($event->getDescription()); ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>
</div>

<?php echo $this->htmlLink($this->url(array('user' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'event_general'), $this->translate('View Upcoming Events'), array('class' => 'buttonlink item_icon_event')) ?>