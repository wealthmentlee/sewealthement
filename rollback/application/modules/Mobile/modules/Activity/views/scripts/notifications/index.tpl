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

<h4>&raquo; <?php echo $this->translate('Notifications');?> </h4>


<?php if( count($this->navigation) > 0 ): ?>
	<div class="tabs">
		<ul>
			<?php foreach( $this->navigation as $item ): ?>

				<?php if ($item->getRoute() == 'recent_activity'):?>
				<li class="active">
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->translate($item->getLabel()) ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
				<li class="content">
					<ul class='items'>
						<?php if( $this->notifications->getTotalItemCount() > 0 ): ?>
							<?php foreach( $this->notifications as $notification ):
								ob_start();
								try { ?>
									<li class="<?php if( !$notification->read ): ?>unread<?php else: ?>read<?php endif; ?>" >
										<span class="notification_item_general">
											<?php echo $notification->__toString() ?>
										</span>
									</li>
								<?php
								} catch( Exception $e ) {
									ob_end_clean();
									if( APPLICATION_ENV === 'development' ) {
										echo $e->__toString();
									}
									continue;
								}
								ob_end_flush();
								endforeach;
							?>
						<?php else: ?>
							<li>
								<?php echo $this->translate("You have no notifications.") ?>
							</li>
						<?php endif; ?>
					</ul>

					<div class="notifications_options">
						<?php echo $this->paginationControl($this->notifications, null, array('pagination/search.tpl', 'mobile') ); ?>
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