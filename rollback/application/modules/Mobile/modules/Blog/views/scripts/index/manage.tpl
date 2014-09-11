<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: manage.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<h4>&raquo; <?php echo $this->translate('My Blogs');?></h4>

<?php if( count($this->navigation) > 0 ): ?>
	<div class="tabs">
		<ul>
			<?php foreach( $this->navigation as $item ): ?>

				<?php if($item->getClass() == 'menu_blog_main blog_main_manage'):?>
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
										<div class='item_options'>
											<a href="<?php echo $this->url(array('action' => 'delete','blog_id' => $item->getIdentity(), 'return_url'=>urlencode($_SERVER['REQUEST_URI'])), 'blog_specific', true); ?>">
												<img src="application/modules/Mobile/externals/images/referrers_clear.png" border="0"/>
											</a>
										</div>

										<div class='item_body'>
											<p>
												<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
                                                <?php echo $this->mobileItemRate('blog', $item->getIdentity())?>
												<br/>
												<?php
													// Not mbstring compat
													echo Engine_String::substr(Engine_String::strip_tags($item->body), 0, 50); if (Engine_String::strlen($item->body)>49) echo "...";
												?>
											</p>
											<div class='item_date'>
												<?php echo $this->translate('Posted by');?>
												<?php echo $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle()) ?>
												<?php echo $this->translate('about');?>
												<?php echo $this->timestamp(strtotime($item->creation_date)) ?>
											</div>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>

							<?php elseif($this->search): ?>
								<div class="tip">
									<span>
										<?php echo $this->translate('You do not have any blog entries that match your search criteria.');?>
									</span>
								</div>
							<?php else: ?>
								<div class="tip">
									<span>
										<?php echo $this->translate('You do not have any blog entries.');?>
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