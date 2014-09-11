<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: requests.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<h4>&raquo; <?php echo $this->translate('Notifications');?> </h4>

<?php if( count($this->navigation) > 0 ): ?>
	<div class="tabs">
		<ul>
			<?php foreach( $this->navigation as $item ): ?>

				<?php if ($item->action == 'requests'):?>
				<li class="active">
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->translate($item->getLabel()) ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
				<li class="content">
						<?php if( $this->requests->getTotalItemCount() > 0 ): ?>
							<?php foreach( $this->requests as $notification ): ?>
								<?php
									$parts = explode('.', $notification->getTypeInfo()->handler);
                  try{
									  echo $this->mobileAction($parts[2], $parts[1], $parts[0], array('notification' => $notification));
                  }
                  catch (Exception $e){}
								?>
							<?php endforeach; ?>
						<?php else: ?>
							<div style="text-align:center">

								<?php echo $this->translate("You have no requests.") ?>

							</div>
						<?php endif; ?>
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