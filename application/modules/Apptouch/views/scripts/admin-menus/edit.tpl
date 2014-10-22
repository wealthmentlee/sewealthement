<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: edit.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */
$staticBaseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
  ->prependStylesheet($staticBaseUrl . 'application/modules/Apptouch/externals/styles/jqm-icon-pack-2.0-original.css');
?>

<?php if ($this->form): ?>

<?php echo $this->form->render($this) ?>

<script type="text/javascript">
    window.addEvent('domready', function(){
       $$('#icon-element option').each(function(item){
           var elem = new Element('span');
           elem.set('class', 'apptouch_menu_editor_icons ui-icon-' + item.value);
           item.grab(elem, 'top');
       });
    });
</script>

<?php elseif ($this->status): ?>

<div><?php echo $this->translate("Changes saved!") ?></div>

<script type="text/javascript">
  var name = '<?php echo $this->name ?>';
  var label = '<?php echo $this->escape($this->menuItem->label) ?>';
  setTimeout(function () {
    parent.$('admin_menus_item_' + name).getElement('.item_label').set('html', label);
    parent.Smoothbox.close();
  }, 500);
</script>

<?php endif; ?>