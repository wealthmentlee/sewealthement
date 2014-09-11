<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>&raquo; <?php echo $this->translate('%1$s\'s Blog', $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?> </h4>

<div class="layout_content">
	<ul class="items subcontent">
		<li>

			<div class="item_photo">
				<?php echo $this->htmlLink($this->owner->getHref(), $this->itemPhoto($this->owner, 'thumb.profile'), array('class' => 'blogs_gutter_photo')) ?>
			</div>
			<div class="item_body">
			<?php if (count($this->userCategories )):?>
				<ul class="categories">
						<li> <a href="<?php echo $this->url(array('user_id'=>$this->owner->getIdentity()), 'blog_view', true); ?>"><?php echo $this->translate('All Categories')?></a> </li>
						<?php foreach ($this->userCategories as $category): ?>
							<li> <a href="<?php echo $this->url(array('user_id'=>$this->owner->getIdentity(), 'category'=>$category->category_id), 'blog_view', true); ?>"><?php echo $category->category_name?></a></li>
						<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			</div>
		</li>

		<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
		<?php foreach ($this->paginator as $item): ?>
		<li>
			<div class="item_body">
				<?php echo $this->htmlLink($item->getHref(), $item->getTitle(), array('class'=>'blogentry_title')) ?>

				<div class="item_date">
				 <?php echo $this->translate('by');?> <?php echo $this->htmlLink($item->getParent(), $item->getParent()->getTitle()) ?>
					<?php echo $this->timestamp($item->creation_date) ?>
				</div>

				<div>
					<?php echo $this->mobileSubstr(Engine_String::strip_tags($item->body)) ?>
				</div>

				<div class="item_date">
				<?php if ($item->comment_count > 0) :?>
					<?php echo $this->htmlLink($item->getHref(), $item->comment_count . ' ' . ( $item->comment_count != 1 ? 'comments' : 'comment' )) ?>
				<?php endif; ?>
				</div>

			</div>
		</li>
		<?php endforeach; ?>

		<?php elseif( $this->category || $this->tag ): ?>
		<li>
			<span>
				<?php echo $this->translate('%1$s has not published a blog entry with that criteria.', $this->owner->getTitle()); ?>
			</span>
		</li>

		<?php else: ?>
		<li>
			<span>
				<?php echo $this->translate('%1$s has not written a blog entry yet.', $this->owner->getTitle()); ?>
			</span>
		</li>
		<?php endif; ?>
	</ul>

	<?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
</div>