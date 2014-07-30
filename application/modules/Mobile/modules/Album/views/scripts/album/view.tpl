<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>&raquo;  <?php echo $this->translate('%1$s\'s Album: %2$s',
    $this->album->getOwner()->__toString(),
    ( '' != trim($this->album->getTitle()) ? $this->album->getTitle() : '<em>' . $this->translate('Untitled') . '</em>')
  ); ?> </h4>

<div class="layout_content">
	<?php if( '' != trim($this->album->getDescription()) ): ?>
		<p class="description">
			<?php echo $this->album->getDescription() ?>
		</p>
		<br />
	<?php endif ?>


		<ul class="items">
			<?php foreach( $this->paginator as $photo ): ?>
				<li>
					<a class="thumbs_photo" href="<?php echo $photo->getHref(); ?>">
						<div class="item_photo">
							<img src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" width="80px"/>
						</div>
						<div class="item_body">
							<?php echo $photo->getTitle(); ?>
                            <?php echo $this->mobileItemRate('album_photo', $photo->getIdentity())?>
						</div>
					</a>
				</li>
			<?php endforeach;?>
		 </ul>
	</div>
</div>

<?php if( $this->paginator->count() > 0 ): ?>
	<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
	<br />
<?php endif; ?>

<?php echo $this->mobileAction("list", "comment", "core", array("type"=>"album_photo", "id"=>$this->album->getIdentity(), 'viewAllLikes'=>true)); ?>

<br/>