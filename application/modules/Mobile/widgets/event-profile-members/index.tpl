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

<?php echo $this->form->render()?>

<div class="event_members_info mobile_box">
  <div>
    <?php if ($this->waiting_count):?>

      <?php if ($this->waiting):?>

        <a href="<?php echo $this->url(array(
            'id' => $this->event->getIdentity(),
            'waiting' => 0
          ), 'event_profile', true) . '?tab=' . $this->tab ?>" >
          <?php echo $this->translate('View all approved members'); ?>
        </a>

      <?php else:?>

        <a href="<?php echo $this->url(array(
            'id' => $this->event->getIdentity(),
            'waiting' => 1
          ), 'event_profile', true) . '?tab=' . $this->tab ?>" >
          <?php echo $this->translate('See Waiting'); ?>
        </a>

      <?php endif;?>

    <?php endif; ?>
  </div>

  <div class="event_members_total">
    <?php if( '' == $this->search ): ?>
      <?php echo $this->translate(array('This event has %1$s guest.', 'This event has %1$s guests.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
    <?php else: ?>
      <?php echo $this->translate(array('This event has %1$s guest that matched the query "%2$s".', 'This event has %1$s guests that matched the query "%2$s".', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->search) ?>
    <?php endif; ?>
  </div>
</div>

<?php

$return_url = urlencode($_SERVER['REQUEST_URI']);

?>

<?php if( $this->members->getTotalItemCount() > 0 ): ?>
  <ul class='items'>
    <?php foreach( $this->members as $member ):
      if( !empty($member->resource_id) ) {
        $memberInfo = $member;
        $member = $this->item('user', $memberInfo->user_id);
      } else {
        $memberInfo = $this->event->membership()->getMemberInfo($member);
      }
      ?>

      <li>

        <div class="item_photo">
          <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'event_members_icon')) ?>
        </div>

        <div class='item_body'>

          <div class='item_options'>
            <?php // Add/Remove Friend ?>
            <?php if( $this->viewer()->getIdentity() && !$this->viewer()->isSelf($member) ): ?>
              <?php if( !$this->viewer()->membership()->isMember($member) ): ?>
                <?php echo $this->htmlLink(array('route' => 'user_extended', 'controller' => 'friends', 'action' => 'add', 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Add Friend')) ?><br />
              <?php else: ?>
                <?php echo $this->htmlLink(array('route' => 'user_extended', 'controller'=>'friends', 'action' => 'remove', 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Remove Friend')) ?><br />
              <?php endif; ?>
            <?php endif; ?>
            <?php // Remove/Promote/Demote member ?>
            <?php if( $this->event->isOwner($this->viewer())): ?>
              <?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
                <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Approve Request')) ?><br />
                <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'approve', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Reject Request')) ?><br />
              <?php endif; ?>
              <?php if( $memberInfo->active == false && $memberInfo->resource_approved == true ): ?>
                <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'cancel', 'event_id' => $this->event->getIdentity(), 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Cancel Invite')) ?>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <div>
            <span class='event_members_status'>
              <?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?>

              <?php // Titles ?>
              <?php if( $this->event->getParent()->getGuid() == ($member->getGuid())): ?>
                (<?php echo ( $memberInfo->title ? $memberInfo->title : 'owner' ) ?>)
              <?php endif; ?>

            </span>
            <span>
              <?php echo $member->status; ?>
            </span>
          </div>
          <div class="event_members_rsvp">
            <?php if( $memberInfo->rsvp == 0 ): ?>
              <?php echo $this->translate('Not Attending') ?>
            <?php elseif( $memberInfo->rsvp == 1 ): ?>
              <?php echo $this->translate('Maybe Attending') ?>
            <?php elseif( $memberInfo->rsvp == 2 ): ?>
              <?php echo $this->translate('Attending') ?>
            <?php else: ?>
              <?php echo $this->translate('Awaiting Reply') ?>
            <?php endif; ?>
          </div>

        </div>

      </li>

    <?php endforeach;?>

  </ul>

  <?php if( $this->members->count() > 1 ): ?>
    <?php echo $this->paginationControl($this->members, null, array('pagination/search.tpl', 'mobile'), array(
        'params' => array(
          'waiting' => $this->waiting,
          'search' => $this->search
        ),
        'query' => array(
          'tab' => $this->tab,
        )
    )); ?>
  <?php endif; ?>

<?php endif; ?>