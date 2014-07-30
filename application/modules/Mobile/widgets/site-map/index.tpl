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
<?php if ($this->type == 'list'): ?>
  <ul class="site-map-list">
    <?php foreach( $this->navigation as $item ): ?>
      <li>
				<a href="<?php echo $item->getHref(); ?>"><?php echo $this->translate($item->getLabel()); ?>
					<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
				</a>
			</li>
    <?php endforeach; ?>
	</ul>

<?php elseif($this->type == 'links'): ?>
	<ul class="site-map-links">
		<?php foreach( $this->navigation as $item ): ?>
			<li>
				<a href="<?php echo $item->getHref(); ?>"><?php echo $this->translate($item->getLabel()); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>