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

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <ul class="items">
    <?php foreach( $this->paginator as $event ): ?>
      <li>
        <div class='item_photo'>
          <?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
        </div>
        <div class='item_body'>
          <div class="groups_profile_tab_title">
            <?php echo $this->htmlLink($event->getHref(), $this->string()->chunk($event->getTitle(), 10)) ?>
          </div>
          <span class="groups_profile_tab_members">
            <?php echo $this->translate('By');?>
            <?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
          </span>
          <span class="item_date">
            <?php echo $this->timestamp($event->creation_date) ?>
          </span>
          <div class="groups_profile_tab_desc">
            <?php echo $event->getDescription() ?>
          </div>
        </div>
      </li>
    <?php endforeach;?>
  </ul>

<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('No events have been added to this group yet.');?>
    </span>
  </div>

<?php endif; ?>

<?php if($this->paginator->getTotalItemCount() > 2):?>
  <?php echo $this->htmlLink($this->url(array('group' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'event_general'), $this->translate('MOBILE_View Upcoming Events'), array('class' => 'buttonlink item_icon_event')) ?>
<?php endif;?>
