<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: delete.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<?php if ($this->error): ?>
<ul class="form-errors"><li><ul class="errors"><li><?php echo $this->message; ?></li></ul></li></ul>
<?php return; endif; ?>

<?php echo $this->form->render($this); ?>