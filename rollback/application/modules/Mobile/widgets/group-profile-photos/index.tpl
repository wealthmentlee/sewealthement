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

<?php if( $this->paginator->getTotalItemCount() > 0 || $this->canUpload ): ?>
  <div class="group_album_options mobile_box">
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
      <?php echo $this->htmlLink(array(
          'route' => 'group_extended',
          'controller' => 'photo',
          'action' => 'list',
          'subject' => $this->subject()->getGuid(),
        ), $this->translate('View All Photos'), array(
          'class' => 'buttonlink icon_group_photo_view'
      )) ?>
    <?php endif; ?>
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
      <?php echo $this->translate('No photos have been uploaded to this group yet.');?>
    </span>
  </div>

<?php endif; ?>