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

<h4>
  &raquo; <?php echo $this->subject->__toString()?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_blog', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_BLOGS')) ?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_blog', 'action' => 'view', 'blog_id' => $this->blog->getIdentity()), ($this->blog->getTitle()) ? $this->blog->getTitle() : $this->translate('Untitled')) ?>
</h4>

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
				</div>
			</h4>
		</div>

	</li>

	<li style="border-top: 0px;">
			<div class="item_body">
				<?php echo $this->blog->body ?>
			</div>
	</li>
</ul>

<div style="padding-bottom: 5px;"></div>

<?php echo $this->mobileAction("list", "comment", "core", array("type"=>"pageblog", "id"=>$this->blog->getIdentity(), 'viewAllLikes'=>true)) ?>

</div>