<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<h4>&raquo; <?php echo $this->event->__toString()." &raquo; ".$this->translate("Photos") ?></h4>

<div class='layout_middle'>
  <?php if( $this->paginator->count() > 0 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
    <br />
  <?php endif; ?>

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

  <?php if( $this->paginator->count() > 0 ): ?>
    <br />
    <?php echo $this->paginationControl($this->paginatorm, null, array('pagination/search.tpl', 'mobile')); ?>
  <?php endif; ?>
</div>