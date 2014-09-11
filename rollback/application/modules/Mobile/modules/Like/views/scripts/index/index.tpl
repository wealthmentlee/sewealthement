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

<script type="text/javascript">
like.url.remove = '<?php echo $this->url(array('action' => 'unlike'), 'like_default'); ?>';
like.clubs.users.count = <?php echo $this->users ? $this->users->getTotalItemCount() : 0; ?>;
like.clubs.pages.count = <?php echo $this->pages ? $this->pages->getTotalItemCount() : 0; ?>;

en4.core.runonce.add(function(){
	like.init_counts();
});
</script>

<?php if ($this->isSelf): ?>
	<h2><?php echo $this->translate("Like Information"); ?></h2>
	<p><?php echo $this->translate("On this page you can find like clubs you are involved."); ?></p>
<?php else: ?>
	<h2><?php echo $this->translate("like_%s's likes", $this->htmlLink($this->user->getHref(), $this->user->getTitle())); ?></h2>
<?php endif; ?>
<br />

<div>
	
<div class="like_profile_navigation">
	<?php echo $this->render('_navigation.tpl');  ?>
</div>

<?php if (count($this->items) > 0): ?>

<div class="like_container">

<?php 
	$nophoto_items = array('blog', 'pageblog', 'poll');
	foreach ($this->items as $key => $data):
?>
	<div id="likes_<?php echo $key; ?>" class="like_club_container <?php if ($this->activeTab != $key): ?>hidden<?php endif; ?>">
		<?php if (count($data) > 0): ?>
		<?php foreach ($data as $item): ?>
			<div class="item <?php if ( in_array($item->getGuid(), $this->mutualItems) ): ?>mutual<?php endif; ?>" id="like_<?php echo $item->getGuid(); ?>">
				<div class="l">
					<?php
            $img = $item->getPhotoUrl('thumb.icon');
            if (!$img) {
              $img = $this->baseUrl().'/application/modules/Like/externals/images/nophoto/' . $item->getType() . '.png';
            }
            $photo = "<img width='48px' height='48px' class='thumb_icon item_photo_".$item->getType()."' src='".$img."' />";
						echo $this->htmlLink($item->getHref(), $photo);
					?>
				</div>
				<div class="r">
					<?php echo $this->htmlLink( $item->getHref(), Engine_String::substr($item->getTitle(), 0, 20), array('class' => 'bold') ); ?>
				</div>
				<?php if ( in_array($item->getGuid(), $this->mutualItems) ): ?>
					<div class="mutual_text"><?php echo $this->translate('mutual'); ?></div>
				<?php endif; ?>
				<?php if ($this->isSelf): ?>
					<div class="op">
						<?php echo $this->htmlLink("javascript:like.remove('{$item->getType()}', {$item->getIdentity()})", '', array('class' => 'remove_like_link')); ?>
					</div>
				<?php endif; ?>
				<div class="clr"></div>
			</div>
		<?php endforeach; ?>
		<?php else: ?>
			<div class="no_result_pages tip hidden"><span><?php echo $this->translate("like_There is no likes."); ?></span></div>
		<?php endif; ?>
	<div class="clr"></div>
	</div>
<?php endforeach; ?>

</div>

<?php else: ?>

<div class="tip">
	<span>
		<?php echo $this->translate("like_You didn't like anything yet."); ?>
	</span>
</div>

<?php endif; ?>

<div class="clr"></div>

</div>