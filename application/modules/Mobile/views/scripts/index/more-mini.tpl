<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: more-mini.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<?php if ( isset($this->navigation) ): ?>
	<?php
		$count = count($this->navigation);
		foreach( $this->navigation->getPages() as $item ) $item->setOrder(--$count);
	?>

	<div id='mobile_profile_options'>
		<ul>
			<?php foreach($this->navigation as $item): ?>
				<li><?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array('class'=>'buttonlink')) ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>