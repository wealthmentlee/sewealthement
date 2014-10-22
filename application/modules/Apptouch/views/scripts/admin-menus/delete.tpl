<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: delete.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>

<?php if ($this->form): ?>

<?php echo $this->form->render($this) ?>

<?php elseif ($this->status): ?>

<div><?php echo $this->translate("Deleted") ?></div>

<script type="text/javascript">
  var name = '<?php echo $this->name ?>';
  setTimeout(function () {
    parent.$('admin_menus_item_' + name).destroy();
    parent.Smoothbox.close();
  }, 500);
</script>

<?php endif; ?>