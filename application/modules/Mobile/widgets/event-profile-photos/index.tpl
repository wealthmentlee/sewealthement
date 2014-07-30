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

<?php if( $this->paginator->getTotalItemCount() > 2): ?>
  <div class="event_album_options mobile_box">
      <?php echo $this->htmlLink(array(
          'route' => 'event_extended',
          'controller' => 'photo',
          'action' => 'list',
          'subject' => $this->subject()->getGuid(),
        ), $this->translate('View All Photos'), array(
          'class' => 'buttonlink icon_event_photo_view'
      )) ?>
  </div>
<?php endif; ?>



<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>

  <ul class="items">
		<?php foreach( $this->paginator as $photo ): ?>
			<li>
				<a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
					<div class="item_photo">
						<img src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" width="80px"/>
					</div>
					<div class="item_body">
            <?php echo $this->translate('By');?>
            <?php echo $this->htmlLink($photo->getOwner()->getHref(), $photo->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
            <br />
            <?php echo $this->timestamp($photo->creation_date) ?>
					</div>
				</a>
			</li>
		<?php endforeach;?>
	 </ul>

<?php else: ?>

  <div class="tip">
    <span>
      <?php echo $this->translate('No photos have been uploaded to this event yet.');?>
    </span>
  </div>

<?php endif; ?>