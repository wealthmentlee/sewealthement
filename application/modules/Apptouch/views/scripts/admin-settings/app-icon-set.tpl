<?php echo $this->content()->renderWidget('apptouch.admin-main-menu', array('active' => 'apptouch_admin_main_settings')) ?>
<h3><?php echo $this->translate("APPTOUCH_Setting Home Screen Icon") ?></h3>
<div class='settings'>

  <div class="apptouch admin_home_right">
    <?php echo $this->content()->renderWidget('apptouch.admin-quick-menu', array('menu_name' => 'apptouch_admin_settings', 'active' => 'apptouch_admin_settings_iconset')); ?>
  </div>
  <div class="admin_home_middle">
    <?php echo $this->form->render($this) ?>
  </div>
</div>
