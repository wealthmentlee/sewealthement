<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _browseUsers.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<div class="layout_content">
<h3>
  <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
</h3>

<ul class="items">
  <?php foreach( $this->users as $user ): ?>
    <li>
			<div class="item_photo">
      	<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
			</div>

      <div class='item_body'>
			<?php if( $this->viewer()->getIdentity() ): ?>
        <div class='item_options'>
          <?php echo $this->mobileUserFriendship($user) ?>
        </div>
      <?php endif; ?>
			<?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
            <?php echo $this->mobileItemRate('user', $user->getIdentity())?>
			<?php echo Engine_String::substr($user->status, 0, 50) . ((Engine_String::strlen($user->status)>49)? '...':''); ?>

			<?php if( $user->status != "" ): ?>
			<div class='item_date'>
				<?php echo $this->timestamp($user->status_date) ?>
			</div>
			<?php endif; ?>
			</div>
    </li>
  <?php endforeach; ?>
</ul>

<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
</div>