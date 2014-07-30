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

<h4>
  &raquo; <?php echo $this->translate('My Pages');?>
  <?php if ($this->userObj && $this->userObj->getIdentity()):?>&raquo; <?php echo $this->userObj->__toString()?><?php endif;?>
</h4>

<?php if( count($this->navigation) > 0 ): ?>
	<div class="tabs">
		<ul>
			<?php foreach( $this->navigation as $item ): ?>

				<?php if ($item->active):?>
				<li class="active">
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->translate($item->getLabel()) ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
				<li class="content">
					<div	class="search">
						<?php echo $this->formFilter->render($this) ?>
					</div>

						<?php if( count($this->paginator) > 0 ): ?>

						<ul class='items'>
							<?php foreach( $this->paginator as $page ): ?>

                <?php $page_id = $page->getIdentity()?>

								<li class="<?php if ($page->featured) echo "active"; ?>">
									<div class="item_photo">
										<?php echo $this->htmlLink($page->getHref(), $this->itemPhoto($page, 'thumb.normal')) ?>
									</div>
									<div class="item_body">

                    <div class="item_options">
                      <?php echo $this->htmlLink($this->url(array('action' => 'delete', 'page_id' => $page->getIdentity()), 'page_team') . '?return_url=' . urlencode($_SERVER['REQUEST_URI']), $this->translate("Delete")); ?>
                    </div>

										<div class="item_title">
											<?php echo $this->htmlLink($page->getHref(), $page->getTitle())?>

                      <?php if ($page->featured) : ?>
                        <span class="page_item_featured"><?php echo $this->translate("Sponsored"); ?></span>
                      <?php endif; ?>

                      <?php echo $this->mobileItemRate('page', $page->getIdentity())?>
										</div>
										<div class="item_date">
                      <?php echo $this->translate("Submitted by"); ?>
       						    <a href="<?php echo $page->getOwner()->getHref(); ?>"><?php echo $page->getOwner()->getTitle(); ?></a>, <?php echo $this->translate("updated"); ?>
       						    <?php echo $this->timestamp($page->modified_date); ?> | <?php echo $page->view_count ?> <?php echo $this->translate("views"); ?>
                    </div>

										<div class="item_desc">
											<?php echo $this->mobileSubstr($page->getDescription()) ?>
										</div>

									</div>
								</li>
							<?php endforeach; ?>
						</ul>

						<?php else: ?>
							<div class="tip">
								<?php echo $this->translate('There is no pages.') ?>
							</div>
						<?php endif; ?>

						<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile'), array('query' => array('search' => $this->search, 'user' => $this->user))); ?>

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