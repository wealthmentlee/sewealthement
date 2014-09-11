<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: inbox.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>&raquo; <?php echo $this->translate('Messages');?></h4>

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
						<?php if( $this->paginator->getTotalItemCount() <= 0 ): ?>
							<div class="tip">
								<span>
									<?php echo $this->translate('Tip: %1$sClick here%2$s to send your first message!', "<a href='".$this->url(array('action' => 'compose'), 'messages_general')."'>", '</a>'); ?>
								</span>
							</div>
						<?php endif; ?>

						<?php if( count($this->paginator) ): ?>
								<ul class="items">
									<?php foreach( $this->paginator as $conversation ):?>
										<li>
									<?php
										$message = $conversation->getInboxMessage($this->viewer());
										$recipient = $conversation->getRecipientInfo($this->viewer());
										if( $conversation->recipients > 1 ) {
											$user = $this->viewer();
										} else {
											foreach( $conversation->getRecipients() as $tmpUser ) {
												if( $tmpUser->getIdentity() != $this->viewer()->getIdentity() ) {
													$user = $tmpUser;
												}
											}
										}

										if( !isset($user) || !$user ) {
											$user = $this->viewer();
										}
										?>

											<div class="item_photo">
												<?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon')) ?>
											</div>
											<div class="item_body <?php if( !$recipient->inbox_read ): ?>unread<?php else: ?>read<?php endif; ?>">
												<div class="item_options">
													<a href="<?php echo $this->url(array('action' => 'delete'), 'messages_general', true) . '?message_id=' . $conversation->getIdentity() . '&return_url=' . urlencode($_SERVER['REQUEST_URI']); ?>">
														<img src="application/modules/Mobile/externals/images/referrers_clear.png" border="0"/>
													</a>
												</div>
												<div>
													<?php if( $conversation->recipients == 1 ): ?>
														<?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
													<?php else: ?>
														<?php echo $conversation->recipients ?> people
													<?php endif; ?>
												</div>
												<div class="item_date">
													<?php echo $this->timestamp($message->date) ?>
												</div>
												<div>
													<?php
														( '' != ($title = trim($message->getTitle())) ||
															'' != ($title = trim($conversation->getTitle())) ||
															$title = $this->translate('(No Subject)') );
													?>
													<?php echo $this->htmlLink($conversation->getHref(), $this->mobileSubstr($this->stripHtmlTag($title), 10)) ?>
												</div>
												<div>
													<?php echo $this->mobileSubstr(Engine_String::strip_tags(html_entity_decode($message->body)), 20) ?>
												</div>
											</div>
										</li>
									<?php endforeach; ?>
								</ul>
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