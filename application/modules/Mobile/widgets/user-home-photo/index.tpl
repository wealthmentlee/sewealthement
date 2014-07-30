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
<h3>
  <?php echo $this->translate('Hi %1$s!', $this->viewer()->getTitle()); ?>
</h3>
<div>
  <?php echo $this->htmlLink($this->viewer()->getHref(), $this->itemPhoto($this->viewer(), 'thumb.profile')) ?>
</div>