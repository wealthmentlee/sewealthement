<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

?>
<?php if( $this->enableComposer ): ?>
	<div>

		<form method="post" action="<?php echo $this->url(array('module' => 'activity', 'controller' => 'index', 'action' => 'post'), 'default', true) ?>" class="global_form_box" enctype="application/x-www-form-urlencoded">
			<?php echo $this->translate("MOBILE_What's on your mind?") ?><br/>
			<textarea id="body" rows="2" name="body"></textarea>
			<input type="hidden" name="return_url" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
			<?php if( $this->viewer() && $this->subject() && !$this->viewer()->isSelf($this->subject())): ?>
				<input type="hidden" name="subject" value="<?php echo $this->subject()->getGuid() ?>" />
			<?php endif; ?>
			<div class="feed-submit">
				<button id="mobile-compose-submit" type="submit"><?php echo $this->translate("Share") ?></button>
			</div>
		</form>
	</div>
<?php endif; ?>

<?php // If requesting a single action and it doesn't exist, show error ?>
<?php if( !$this->activity ): ?>
	<?php if( $this->action_id ): ?>
		<h4>&raquo; <?php echo $this->translate("Activity Item Not Found") ?></h4>
		<p>
			<?php echo $this->translate("The page you have attempted to access could not be found.") ?>
		</p>
	<?php return; else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate("Nothing has been posted here yet - be the first!") ?>
			</span>
		</div>
	<?php return; endif; ?>
<?php endif; ?>

<ul class='feed items'>
	<?php echo $this->mobileActivityLoop($this->activity, array(
		'action_id' => $this->action_id,
		'viewAllComments' => $this->viewAllComments,
		'viewAllLikes' => $this->viewAllLikes,
	))?>
</ul>

<?php if (($this->previd > 0) || ($this->nextid > 0 && !$this->endOfFeed)): ?>
  <div class="pages">
    <ul class="paginationControl">
			<li class="paginator_previous">
				<?php if( $this->previd > 0): ?>
						<?php echo $this->htmlLink(array('reset'=>false, 'minid' => ((int)($this->firstid+1)), 'maxid'=>''),
						'<img src="application/modules/Mobile/themes/' . $this->mobileActiveTheme()->name . '/images/prev.png" alt="' . $this->translate('Prev') . '"/>') ?>
				<?php else: ?>
					<span><img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/prev_disabled.png" alt="<?php echo $this->translate('Prev') ?>"/></span>
				<?php endif; ?>
			</li>

			<li class="paginator_middle">
				<span>
					<?php echo $this->translate('MOBILE_Page %1$s of %2$s', $this->current, $this->pageCount) ?>
				</span>
			</li>

			<li class="paginator_next">
				<?php if( $this->nextid > 0 && !$this->endOfFeed ): ?>
						<?php echo $this->htmlLink(array('reset'=>false, 'maxid' => $this->nextid, 'minid'=>'' ),
						'<img src="application/modules/Mobile/themes/' . $this->mobileActiveTheme()->name . '/images/next.png" alt="' . $this->translate('Next') . '"/>') ?>
				<?php else: ?>
					<span><img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/next_disabled.png" alt="<?php echo $this->translate('Next') ?>"/></span>
				<?php endif; ?>
			</li>

    </ul>
  </div>
<?php endif; ?>

<div class="clr"></div>