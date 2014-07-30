<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2011-02-16 10:08:38 michael $
 * @author     Michael
 */

?>
<h4>
  &raquo; <?php echo $this->question_title ?>
  <?php if ($this->userObj && $this->userObj->getIdentity()):?>&raquo; <?php echo $this->userObj->__toString()?><?php endif;?>
</h4>

<?php if( count($this->navigation) > 0 ): ?>
	<div class="tabs">
		<ul>
			<?php foreach( $this->navigation as $item ): ?>

				<?php if ($item->active):?>
				<li class="active">
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->translate($item->getLabel()) ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
				<li class="content">
          <?php echo $this->form->render($this) ?>
				</li>

				<?php else: ?>
				<li>
					<a href="<?php echo $item->getHref(); ?>">
						<?php echo $this->translate($item->getLabel()) ?>
						<img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/listArrow.png"/>
					</a>
				</li>
				<?php endif; ?>

			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>