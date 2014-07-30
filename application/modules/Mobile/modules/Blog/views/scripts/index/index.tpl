<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<h4>
  &raquo; <?php echo $this->translate('Blogs');?>
  <?php if ($this->userObj && $this->userObj->getIdentity()):?>&raquo; <?php echo $this->userObj->__toString()?><?php endif;?>
</h4>

<?php if( count($this->navigation) > 0 ): ?>
	<div class="tabs">
		<ul>
			<?php foreach( $this->navigation as $item ): ?>

				<?php if($item->active):?>
					<li class="active">
						<a href="<?php echo $item->getHref(); ?>">
							<?php echo $this->translate($item->getLabel()) ?>
							<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
						</a>
					</li>

					<li class="content">
						<div	style="margin-bottom:5px;">
							<?php echo $this->form->render($this) ?>
						</div>

						<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
							<ul class="items">
								<?php foreach( $this->paginator as $item ): ?>
									<li>
										<div class='item_photo'>
											<?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
										</div>

										<div class='item_body'>
											<p class='blogs_browse_info_title'>
												<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                                                <?php echo $this->mobileItemRate('blog', $item->getIdentity())?>
												<br/>
												<?php echo $this->mobileSubstr(Engine_String::strip_tags($item->body)) ?>
											</p>
											<div class='item_date'>
												<?php echo $this->translate('Posted');?>
												<?php echo $this->timestamp(strtotime($item->creation_date)) ?>
												<?php echo $this->translate('by');?>
												<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
											</div>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>

						<?php elseif( $this->search ):?>
							<div class="tip">
								<span>
									<?php echo $this->translate('Nobody has written a blog entry with that criteria.');?>
								</span>
							</div>

						<?php else:?>
							<div class="tip">
								<span>
									<?php echo $this->translate('Nobody has written a blog entry yet.'); ?>
								</span>
							</div>
						<?php endif; ?>

						<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
					</li>

					<?php else: ?>
					<li>
						<a href="<?php echo $item->getHref(); ?>">
							<?php echo $this->translate($item->getLabel()) ?>
							<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
						</a>
					</li>
				<?php endif; ?>
			
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>