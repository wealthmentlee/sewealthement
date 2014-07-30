<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _navigation.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<div class="like_navigation">
<?php $counter = 0; ?>
<?php foreach ($this->modules as $type): ?>
	<div class="like_navigation_item <?php if ($this->activeTab == $type): ?>active<?php endif; ?> <?php if ($counter == 0): ?>first<?php endif; ?>">
		<a href="javascript:void(0)" style="background-image: url(<?php echo $this->baseUrl().$this->icons[$type]; ?>);" onClick="like.list('<?php echo $type; ?>', this)">
			<span><?php echo $this->translate($this->labels[$type]); ?> <span class="misc">(<?php echo count($this->items[$type]); ?>)</span></span>
		</a>
	</div>
	<?php $counter++; ?>
<?php endforeach; ?>
</div>