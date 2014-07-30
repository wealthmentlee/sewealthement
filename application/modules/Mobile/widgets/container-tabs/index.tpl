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
<div class='tabs'>
  <ul>
    <?php foreach( $this->tabs as $key => $tab ): ?>

			<?php if( $this->activeTab == $tab['id'] ):?>
				<li class="active">
					<a>
						<?php echo $this->translate($tab['title']) ?> <?php if( !empty($tab['childCount']) ): ?><span>(<?php echo $tab['childCount'] ?>)</span><?php endif; ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
				<li class="content"><?php echo $this->childrenContent ?></li>

			<?php else: ?>
			<li <?php if( $this->activeTab == $tab['id'] ) echo 'class="active"'; ?>">
				<a href="<?php echo ($tab['widget_url']) ? $tab['widget_url'] : $_SERVER['REDIRECT_URL'] . '?tab=' .$tab['id']?>">
					<?php echo $this->translate($tab['title']) ?> <?php if( !empty($tab['childCount']) ): ?><span>(<?php echo $tab['childCount'] ?>)</span><?php endif; ?>
					<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
				</a>
			</li>
			<?php endif; ?>

    <?php endforeach; ?>
  </ul>
</div>