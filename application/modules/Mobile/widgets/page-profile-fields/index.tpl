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

<div class="profile_fields">
	<h4><span><?php echo $this->translate("Page Details"); ?></span></h4>
	<ul>
	<?php if ($this->subject->getTitle()): ?>
		<li>
			<span><?php echo $this->translate("Title"); ?></span><span><?php echo $this->subject->getTitle(); ?></span>
		</li>
	<?php endif; ?>
	<?php if ($this->subject->getDescription()): ?>
		<li>
			<span><?php echo $this->translate("Description"); ?></span><span><?php echo $this->subject->getDescription(); ?></span>
		</li>
	<?php endif; ?>
	<?php if ($this->subject->isAddress()): ?>
		<li>
			<span><?php echo $this->translate("Address"); ?></span><span><?php echo $this->subject->getAddress(); ?></span>
		</li>
	<?php endif; ?>
	<?php if ($this->subject->website): ?>
		<li>
			<span><?php echo $this->translate("Website"); ?></span><span><?php echo $this->subject->getWebsite(); ?></span>
		</li>
	<?php endif; ?>
	<?php if ($this->subject->phone): ?>
		<li>
			<span><?php echo $this->translate("Phone"); ?></span><span><?php echo $this->subject->phone; ?></span>
		</li>
	<?php endif; ?>
	</ul>
</div>

<?php echo $this->fieldValueLoop($this->subject, $this->fieldStructure); ?>