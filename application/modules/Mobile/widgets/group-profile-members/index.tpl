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

<div class="group_members_info mobile_box">
  <div>
    <?php if ($this->waiting_count):?>

      <?php if ($this->waiting):?>

        <a href="<?php echo $this->url(array(
            'id' => $this->group->getIdentity(),
            'waiting' => 0
          ), 'group_profile', true) . '?tab=' . $this->tab ?>" >
          <?php echo $this->translate('View all approved members'); ?>
        </a>

      <?php else:?>

        <a href="<?php echo $this->url(array(
            'id' => $this->group->getIdentity(),
            'waiting' => 1
          ), 'group_profile', true) . '?tab=' . $this->tab ?>" >
          <?php echo $this->translate('See Waiting'); ?>
        </a>

      <?php endif;?>

    <?php endif; ?>
  </div>

  <div class="group_members_total">
    <?php if( '' == $this->search ): ?>
      <?php echo $this->translate(array('This group has %1$s member.', 'This group has %1$s members.', $this->members->getTotalItemCount()),$this->locale()->toNumber($this->members->getTotalItemCount())) ?>
    <?php else: ?>
      <?php echo $this->translate(array('This group has %1$s member that matched the query "%2$s".', 'This group has %1$s members that matched the query "%2$s".', $this->members->getTotalItemCount()), $this->locale()->toNumber($this->members->getTotalItemCount()), $this->search) ?>
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
        $memberInfo = $this->group->membership()->getMemberInfo($member);
      }
      $listItem = $this->list->get($member);
      $isOfficer = ( null !== $listItem );
      ?>

      <li>
        <div class="item_photo">
          <?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon')) ?>
        </div>
        <div class='item_body'>
          <div class='item_options'>

            <?php // Remove/Promote/Demote member ?>
            <?php if( $this->group->isOwner($this->viewer()) ): ?>

              <?php if( !$this->group->isOwner($member) && $memberInfo->active == true ): ?>
                <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'remove', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Remove Member')) ?><br />
              <?php endif; ?>
              <?php if( $memberInfo->active == false && $memberInfo->resource_approved == false ): ?>
                <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'approve', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Approve Request')) ?><br />
                <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'reject', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Reject Request')) ?><br />
              <?php endif; ?>
              <?php if( $memberInfo->active == false && $memberInfo->resource_approved == true ): ?>
                <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'remove', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Cancel Invite')) ?><br />
              <?php endif; ?>


              <?php if( $memberInfo->active ): ?>
                <?php if( $isOfficer ): ?>
                  <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'demote', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Demote Officer')) ?><br />
                <?php elseif( !$this->group->isOwner($member) ): ?>
                  <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'promote', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity(), 'return_url' => $return_url), $this->translate('Make Officer')) ?>
                <?php endif; ?>
              <?php endif; ?>
            <?php endif; ?>
          </div>
          <div>
            <span class='group_members_status'>
              <?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?>

              <?php // Titles ?>
              <?php if( $this->group->isOwner($member) ): ?>
                (<?php echo ( $memberInfo->title ? $memberInfo->title : $this->translate('owner') ) ?><?php if( $this->group->isOwner($this->viewer()) ): ?><?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'edit', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity(), 'format' => 'smoothbox'), '&nbsp;', array('class' => 'smoothbox')) ?><?php endif; ?>)

              <?php elseif( $isOfficer ): ?>
                (<?php echo ( $memberInfo->title ? $memberInfo->title : $this->translate('officer') ) ?><?php if( $this->group->isOwner($this->viewer()) ): ?><?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'edit', 'group_id' => $this->group->getIdentity(), 'user_id' => $member->getIdentity(), 'format' => 'smoothbox'), '&nbsp;', array('class' => 'smoothbox')) ?><?php endif; ?>)
              <?php endif; ?>


            </span>
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