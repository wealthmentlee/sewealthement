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

<h4>&raquo; <?php echo $this->translate('My Albums');?></h4>

<?php if( count($this->navigation) > 0 ): ?>
	<div class="tabs">
		<ul>
			<?php foreach( $this->navigation as $item ): ?>

				<?php if ($item->action == 'manage'):?>
				<li class="active">
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->translate($item->getLabel()) ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
				<li class="content">

					<div	style="margin-bottom:5px;">
						<?php echo $this->search_form->render($this) ?>
					</div>

					<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
						<ul class='items'>
							<?php foreach( $this->paginator as $album ): ?>
								<li>

									<div class="item_photo">
										<?php echo $this->htmlLink($album->getHref(), $this->itemPhoto($album, 'thumb.normal')) ?>
									</div>

									<div class="item_options">
										<a href="<?php echo $this->url(array('action' => 'delete', 'album_id' => $album->album_id, 'return_url'=>urlencode($_SERVER['REQUEST_URI'])), 'album_specific', true); ?>">
											<img src="application/modules/Mobile/externals/images/referrers_clear.png" border="0"/>
										</a>
									</div>

									<div class="item_body">
										<?php echo $this->htmlLink($album->getHref(), Engine_String::substr($album->getTitle(), 0, 15) . ((Engine_String::strlen($album->getTitle()) > 15)? '...':'')) ?>
										<div>
											<?php echo Engine_String::substr($album->getDescription(), 0, 50) . ((Engine_String::strlen($album->getDescription()) > 49)? '...':''); ?>
										</div>
										<div class="item_date">
											<?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
										</div>
									</div>
								</li>
							<?php endforeach; ?>
							<?php if( $this->paginator->count() > 1 ): ?>
								<br />
								<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
							<?php endif; ?>
						</ul>
					<?php else: ?>
						<div class="tip">
							<span>
								<?php echo $this->translate('You do not have any albums yet.');?>
							</span>
						</div>
					<?php endif; ?>
				</div>
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