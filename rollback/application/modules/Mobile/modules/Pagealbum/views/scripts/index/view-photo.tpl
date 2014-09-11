<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view-photo.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<h4>
  &raquo; <?php echo $this->subject->__toString()?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_album', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_ALBUMS')) ?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_album', 'action' => 'view', 'album_id' => $this->album->getIdentity()), ($this->album->getTitle()) ? $this->album->getTitle() : $this->translate('Untitled')) ?>
</h4>

<div class="layout_content">
	<?php if (""!=$this->album->getDescription()): ?>
  <p class="description">
    <?php echo $this->album->getDescription() ?>
  </p>
	<?php endif ?>
	<br/>

	<div>
		<?php if (!$this->message_view):?>

		<?php if ($this->album->count() > 1): ?>

			<ul class="paginationControl">
				<li class="paginator_previous">
					<a href="<?php echo $this->url(array('action' => 'view-photo', 'photo_id' => $this->photo->getPrevCollectible()->getIdentity()), 'page_album') ?>" style="display: inline-block">
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/prev.png" alt="<?php echo $this->translate('Prev') ?>"/>
					</a>
				</li>

				<li class="paginator_middle">
					<span>
					<?php echo $this->translate('Photo %1$s of %2$s in %3$s',
															$this->locale()->toNumber($this->photo->getCollectionIndex() + 1),
															$this->locale()->toNumber($this->album->count()),
															(string) $this->album->getTitle()) ?>
					</span>
				</li>

				<li class="paginator_next">
					<a href="<?php echo $this->url(array('action' => 'view-photo', 'photo_id' => $this->photo->getNextCollectible()->getIdentity()), 'page_album') ?>" style="display: inline-block">
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/next.png" alt="<?php echo $this->translate('Next') ?>"/>
					</a>
				</li>
			</ul>

			<?php else: ?>
			<div>
				<?php echo $this->translate('Photo %1$s of %2$s in %3$s',
															$this->locale()->toNumber($this->photo->getCollectionIndex() + 1),
															$this->locale()->toNumber($this->album->count()),
															(string) $this->album->getTitle()) ?>
			</div>
			<?php endif; ?>
		<?php endif;?>
		<div class="clr"></div>

		<div class='photo_view_container'>

			<div class="photo">
				<a href='<?php echo $this->url(array('action' => 'view-photo', 'photo_id' => $this->photo->getNextCollectible()->getIdentity()), 'page_album') ?>'>
					<?php echo $this->htmlImage($this->photo->getPhotoUrl('thumb'), $this->photo->getTitle()); ?>
				</a>
			</div>

      <?php echo $this->content()->renderWidget('mobile.rate-widget')?>

			<?php if( $this->photo->getTitle() ): ?>
				<div class="title">
					<?php echo $this->photo->getTitle(); ?>
				</div>
			<?php endif; ?>

			<?php if( $this->photo->getDescription() ): ?>
				<div class="caption">
					<?php echo $this->photo->getDescription() ?>
				</div>
			<?php endif; ?>

			<div class="date">
				<?php echo $this->translate('Added');?> <?php echo $this->timestamp($this->photo->modified_date) ?>
			</div>
		</div>
		<br/>
		<?php echo $this->mobileAction("list", "comment", "core", array("type"=>"pagealbumphoto", "id"=>$this->photo->getIdentity(), 'viewAllLikes'=>true)); ?>
	</div>
</div>