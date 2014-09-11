<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: contact.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<?php if( $this->status ): ?>
  <div class="mobile_box"><?php echo $this->message; ?></div>
<?php else: ?>
  <?php echo $this->form->render($this) ?>
<?php endif; ?>