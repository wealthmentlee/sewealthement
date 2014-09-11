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

<h4>&raquo; <?php echo $this->translate('Groups');?> </h4>

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
							<?php foreach( $this->paginator as $group ): ?>
								<li>
									<div class="item_photo">
										<?php echo $this->htmlLink($group->getHref(), $this->itemPhoto($group, 'thumb.normal')) ?>
									</div>
									<div class="item_options">
										<?php if( $group->isOwner($this->viewer()) ): ?>
										<a href="<?php echo $this->url(array('module' => 'group', 'controller' => 'group', 'action' => 'delete', 'group_id' => $group->getIdentity(), 'return_url'=>urlencode($_SERVER['REQUEST_URI'])), 'default', true); ?>">
											<img src="application/modules/Mobile/externals/images/referrers_clear.png" border="0" alt="<?php echo $this->translate('Delete'); ?>"/>
										</a>
										<?php elseif( !$group->membership()->isMember($this->viewer(), null) ): ?>
											<?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'join', 'group_id' => $group->getIdentity()), $this->translate('Join Group')) ?>
										<?php elseif( $group->membership()->isMember($this->viewer(), true) && !$group->isOwner($this->viewer()) ): ?>
											<?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'leave', 'group_id' => $group->getIdentity()), $this->translate('Leave Group')) ?>
										<?php endif; ?>
									</div>
									<div class="item_body">
										<div class="item_title">
											<?php echo $this->htmlLink($group->getHref(), $group->getTitle()) ?>
                                            <?php echo $this->mobileItemRate('group', $group->getIdentity())?>
										</div>
										<div class="item_date">
											<?php echo $this->translate(array('%s member', '%s members', $group->membership()->getMemberCount()),$this->locale()->toNumber($group->membership()->getMemberCount())) ?>
											<?php echo $this->translate('led by');?> <?php echo $this->htmlLink($group->getOwner()->getHref(), $group->getOwner()->getTitle()) ?>
										</div>
										<div class="item_desc">
											<?php echo $this->mobileSubstr($group->getDescription()) ?>
										</div>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
						<?php if( count($this->paginator) > 1 ): ?>
							<div>
								<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
							</div>
						<?php endif; ?>

					<?php else: ?>
						<div class="tip">
							<?php echo $this->translate('You have not joined any groups yet.') ?>
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


