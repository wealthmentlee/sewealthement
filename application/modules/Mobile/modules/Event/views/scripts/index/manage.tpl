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
<h4>&raquo; <?php echo $this->translate('Events');?></h4>

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

            <div style="margin-bottom:5px;">
  						<?php echo $this->formFilter->render($this) ?>
						</div>

						<?php if( count($this->paginator) > 0 ): ?>
								<ul class='items'>
									<?php foreach( $this->paginator as $event ): ?>
										<li>
											<div class="item_photo">
												<?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
											</div>
											<div class="item_body">
                        <div class="item_options">
                          <?php if( $this->viewer() && $event->isOwner($this->viewer()) ): ?>
                            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'event', 'controller' => 'event', 'action' => 'delete', 'event_id' => $event->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), $this->translate('Delete'))?>
                          <?php endif;?>
                          <?php if( $this->viewer() && !$event->membership()->isMember($this->viewer(), null) ): ?>
                            <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller'=>'member', 'action' => 'join', 'event_id' => $event->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), $this->translate('Join Event')) ?>
                          <?php elseif( $this->viewer() && $event->membership()->isMember($this->viewer()) && !$event->isOwner($this->viewer()) ): ?>
                            <?php echo $this->htmlLink(array('route' => 'event_extended', 'controller'=>'member', 'action' => 'leave', 'event_id' => $event->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), $this->translate('Leave Event')) ?>
                          <?php endif; ?>
                        </div>
												<div class="events_title">
													<?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?>
                                                    <?php echo $this->mobileItemRate('event', $event->getIdentity())?>
												</div>
												<div class="item_date">
													<?php echo $this->locale()->toDateTime($event->starttime) ?>
												</div>
												<div>
													<?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()),$this->locale()->toNumber($event->membership()->getMemberCount())) ?>
													<?php echo $this->translate('led by') ?>
													<?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
												</div>
												<div class="events_desc">
													<?php echo $event->getDescription() ?>
												</div>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>

								<?php if( $this->paginator->count() > 1 ): ?>
									<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile'), array(
										'params' => array('view'=>$this->view, 'search'=>$this->search, 'user' => $this->user)
									)); ?>
								<?php endif; ?>
						<?php else: ?>
							<div class="tip">
								<span>
									<?php echo $this->translate('You have not joined any events yet.') ?>
								</span>
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