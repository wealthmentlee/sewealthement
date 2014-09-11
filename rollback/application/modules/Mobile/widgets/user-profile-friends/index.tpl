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
<ul class='items'>
  
  <?php foreach( $this->friends as $membership ):
    if( !isset($this->friendUsers[$membership->user_id]) ) continue;
    $member = $this->friendUsers[$membership->user_id];
    ?>

    <li>
			<div class="item_photo">
      	<?php echo $this->htmlLink($member->getHref(), $this->itemPhoto($member, 'thumb.icon'), array('class' => 'profile_friends_icon')) ?>
			</div>

      <div class='item_body'>
				<div class='item_options'>
        	<?php echo $this->mobileUserFriendship($member) ?>
      	</div>

        <div class='item_title'>
          <?php echo $this->htmlLink($member->getHref(), $member->getTitle()) ?>
        </div>
      </div>

    </li>

  <?php endforeach;?>
</ul>

<?php if($this->friends->count() > 1):?>
  <?php echo $this->htmlLink($this->url(array('module'=>'user', 'controller'=>'friends', 'action'=>'friends', 'user_id' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'default'), $this->translate('View All Friends'), array('class' => 'buttonlink icon_blog_viewall')) ?>
<?php endif;?>