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
  <?php foreach( $this->paginator as $album ): ?>
    <li>
			<div class="item_photo">
				<a href="<?php echo $album->getHref(); ?>">
					<img src="<?php echo $album->getPhotoUrl('thumb.normal'); ?>" width="60px"/>
				</a>
			</div>
			<div class="item_body">
				<span>
					<?php echo $this->htmlLink($album, $this->string()->chunk(Engine_String::substr($album->getTitle(), 0, 45), 10)) ?>
				</span>
				<div class="item_date">
					<?php echo $this->translate('By');?>
					<?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
					-
					<?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
				</div>
			</div>
    </li>
  <?php endforeach;?>
</ul>
</div>

<?php echo $this->htmlLink($this->url(array('user' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'album_general'), $this->translate('View All Albums'), array('class' => 'buttonlink item_icon_album_photo')) ?>
