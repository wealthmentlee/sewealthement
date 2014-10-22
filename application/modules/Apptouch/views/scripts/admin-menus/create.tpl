<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: create.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */
$staticBaseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
  ->prependStylesheet($staticBaseUrl . 'application/modules/Apptouch/externals/styles/jqm-icon-pack-2.0-original.css');
//  ->prependStylesheet($staticBaseUrl . 'application/modules/Apptouch/externals/styles/font-awesome.css');
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
  setTimeout(function () {
    parent.Smoothbox.close();
    parent.window.location.replace(parent.window.location.href)
  }, 500);
</script>

<?php endif; ?>