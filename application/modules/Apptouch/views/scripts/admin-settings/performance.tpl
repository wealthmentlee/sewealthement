<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: performance.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     John
 */
?>

<?php echo $this->content()->renderWidget('apptouch.admin-main-menu', array('active' => 'apptouch_admin_main_settings')) ?>
<h3><?php echo $this->translate("APPTOUCH_Performance Settings") ?></h3>

<div class='settings'>
  <div class="apptouch admin_home_right">
    <?php echo $this->content()->renderWidget('apptouch.admin-quick-menu', array('menu_name' => 'apptouch_admin_settings', 'active' => 'apptouch_admin_settings_performance')); ?>
  </div>
  <div class="admin_home_middle">
    <?php echo $this->form->render($this) ?>
  </div>
</div>

<div id="message" style="display:none;">
  <?php echo $this->message ?>
</div>
