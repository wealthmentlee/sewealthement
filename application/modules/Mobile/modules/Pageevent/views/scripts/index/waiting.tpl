<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: waiting.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>


<h4>
  &raquo; <?php echo $this->subject->__toString()?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_event', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_EVENTS')) ?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_event', 'action' => 'view', 'event_id' => $this->event->getIdentity()), ($this->event->getTitle()) ? $this->event->getTitle() : $this->translate('Untitled')) ?>
  &raquo; <?php echo $this->translate('MOBILE_PAGE_EVENT_MEMBERS_WAITING');?>
</h4>

<?php if (count($this->paginator)):?>

<ul class="items">

<?php foreach ($this->paginator as $item):?>

    <li>
      <div class="item_photo">
        <?php echo $this->itemPhoto($item, 'thumb.icon')?>
      </div>
      <div class="item_body">

        <div class="item_options">
          <?php if (!$item->user_approved):?>
            <a href="<?php echo $this->url(array('action' => 'resource-approve', 'event_id' => $this->event_id, 'approve' => 0, 'user_id' => $item->getIdentity()), 'page_event', true)?>" class="cancel"><?php echo $this->translate('PAGEEVENT_INVITE_CANCEL');?></a>
          <?php else:?>
            <a href="<?php echo $this->url(array('action' => 'resource-approve', 'event_id' => $this->event_id, 'approve' => 1, 'user_id' => $item->getIdentity()), 'page_event', true)?>" class="accept"><?php echo $this->translate('PAGEEVENT_APPROVE');?></a><br />
            <a href="<?php echo $this->url(array('action' => 'resource-approve', 'event_id' => $this->event_id, 'approve' => 0, 'user_id' => $item->getIdentity()), 'page_event', true)?>" class="reject"><?php echo $this->translate('PAGEEVENT_REJECT');?></a>
          <?php endif;?>
        </div>

        <div class="item_title">
          <?php echo $item->__toString()?>
        </div>
      </div>

    </li>

<?php endforeach;?>

</ul>

<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>

<?php else:?>

    <div><?php echo $this->translate('MOBILE_NO_ITEMS')?></div>

<?php endif;?>


