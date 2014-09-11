<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<?php if( !$this->blog || ($this->blog->draft==1 && !$this->blog->isOwner($this->viewer()))): ?>
<?php echo $this->translate('The blog you are looking for does not exist or has not been published yet.');?>
<?php return; // Do no render the rest of the script in this mode
endif; ?>

<h4>&raquo; <?php echo $this->translate('%1$s\'s Blog', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?> </h4>

<div class="layout_content">
<ul class="items subcontent">
	<li>
		<div class="item_photo">
			<?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner, 'thumb.profile'), array('class' => 'blogs_gutter_photo')) ?>
		</div>
		<div class="item_body">
			<h3><?php echo $this->blog->getTitle() ?></h3>
			<h4>
				<div class="item_date" style="font-weight:normal; font-size: 0.9em;">
					<?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?>
					<?php echo $this->timestamp($this->blog->creation_date) ?>
					<?php if ($this->category):?>- <?php echo $this->translate('Filed in');?> <a href='<?php echo $this->url(array('user_id'=>$this->owner->getIdentity(), 'category'=>$this->category->category_id), 'blog_view', true); ?>'><?php echo $this->category->category_name ?></a> <?php endif; ?>
				</div>
			</h4>

			<?php if (count($this->userCategories )):?>
				<ul class="categories">
						<li><a href="<?php echo $this->url(array('user_id'=>$this->owner->getIdentity()), 'blog_view', true); ?>"><?php echo $this->translate('All Categories')?></a></li>
						<?php foreach ($this->userCategories as $category): ?>
							<li> <a href="<?php echo $this->url(array('user_id'=>$this->owner->getIdentity(), 'category'=>$category->category_id), 'blog_view', true); ?>"><?php echo $category->category_name?></a></li>
						<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</li>

  <?php echo $this->content()->renderWidget('mobile.rate-widget')?>

	<?php if ($this->blog->owner_id == $this->viewer->getIdentity()&&$this->blog->draft == 1):?>
	<li style="border-top: 0px">
	<div>
		<span>
			<?php echo $this->translate('This blog entry has not been published. You can publish it by %1$sediting the entry%2$s.'); ?>
		</span>
	</div>
	</li>
	<?php endif; ?>

	<li style="border-top: 0px;">
			<div class="item_body">
				<?php echo $this->blog->body ?>
			</div>
	</li>
</ul>

<div style="padding-bottom: 5px;"></div>

<?php echo $this->mobileAction("list", "comment", "core", array("type"=>"blog", "id"=>$this->blog->getIdentity(), 'viewAllLikes'=>true)) ?>

</div>