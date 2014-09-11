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

<?php if (!$this->isTouchEnabled || $this->isMobile()):?>

<nobr>
	<div class="mode-switcher">

    <?php if ($this->isMobile()):?>

      <a href="<?php echo $this->url(array('mode'=>'standard'), 'mode_switch'); ?>?return_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"><?php echo $this->translate($this->standard); ?></a>
        &#8212;

      <?php if ( $this->isTouchEnabled ): ?>
        <a href="<?php echo $this->url(array('mode'=>'touch'), 'mode_switch'); ?>?return_url=<?php echo urlencode((($this->viewer()->getIdentity())? $this->url(array('action' => 'home'), 'user_general', true) : $this->url(array(), 'home', true)).'#'.$_SERVER['REQUEST_URI']); ?>"><?php echo $this->translate($this->touch); ?></a>
        &#8212;
      <?php endif; ?>

      <?php echo $this->translate($this->mobile); ?>

    <?php else:?>

      <?php echo $this->translate($this->standard); ?>
        &#8212;

      <?php if ( $this->isTouchEnabled ): ?>
        <a href="<?php echo $this->url(array('mode'=>'touch'), 'mode_switch'); ?>?return_url=<?php echo urlencode((($this->viewer()->getIdentity())? $this->url(array('action' => 'home'), 'user_general', true) : $this->url(array(), 'home', true)).'#'.$_SERVER['REQUEST_URI']); ?>"><?php echo $this->translate($this->touch); ?></a>
        &#8212;
      <?php endif; ?>

      <a href="<?php echo $this->url(array('mode'=>'mobile'), 'mode_switch'); ?>?return_url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"><?php echo $this->translate($this->mobile); ?></a>

    <?php endif;?>

	</div>
</nobr>

<?php endif ?>