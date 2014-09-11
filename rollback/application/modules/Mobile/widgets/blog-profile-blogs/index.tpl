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

<div class="layout_content">
<ul class="items">
		<?php foreach ($this->paginator as $item): ?>
			<li>
				<div class='item_body'>
					<p>
						<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
					</p>
					<p class='item_date'>
						<?php echo $this->translate('Posted');?> <?php echo $this->timestamp($item->creation_date) ?>
					</p>
					<p>
						<?php echo $this->mobileSubstr(Engine_String::strip_tags($item->body)); ?>
					</p>
				</div>
			</li>
		<?php endforeach; ?>
	</ul>
</div>

<?php echo $this->htmlLink($this->url(array('user_id' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'blog_view'), $this->translate('View All Entries'), array('class' => 'buttonlink icon_blog_viewall')) ?>
