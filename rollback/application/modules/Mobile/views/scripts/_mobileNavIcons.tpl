<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _mobileNavIcons.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<ul>
  <?php foreach($this->container as $link ):?>
    <?php
      $show_menu = !(isset($link->params['controller']) && $link->params['controller'] == 'report');
    ?>
    <?php if ($show_menu): ?>
    <li>
    <?php $delim = (!strpos($link->getHref(), '?') ? '?' : '&'); ?>
      <a href="<?php echo $link->getHref() . $delim .'return_url=' . urlencode($_SERVER['REQUEST_URI']); ?>" class="buttonlink">
        <?php echo $this->translate($link->getLabel()) ?>
      </a>
    <?php endif; ?>
  <?php endforeach; ?>
</ul>