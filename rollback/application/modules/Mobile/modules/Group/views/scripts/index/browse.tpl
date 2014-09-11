<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: browse.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<h4>
  &raquo; <?php echo $this->translate('Groups');?>
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
							<?php foreach( $this->paginator as $group ): ?>
								<li>
									<div class="item_photo">
										<?php echo $this->htmlLink($group->getHref(), $this->itemPhoto($group, 'thumb.normal')) ?>
									</div>
									<div class="item_body">
                    <div class="item_options">

                      <?php if( !$group->isOwner($this->viewer()) ): ?>

                        <?php if( !$group->membership()->isMember($this->viewer(), null) ): ?>
                          <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'join', 'group_id' => $group->getIdentity()), $this->translate('Join Group')) ?>
                        <?php elseif( $group->membership()->isMember($this->viewer(), true) && !$group->isOwner($this->viewer()) ): ?>
                          <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'member', 'action' => 'leave', 'group_id' => $group->getIdentity()), $this->translate('Leave Group')) ?>
                        <?php endif; ?>

                      <?php endif; ?>

                    </div>
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

						<?php else: ?>
							<div class="tip">
								<?php echo $this->translate('There are no groups yet.') ?>
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