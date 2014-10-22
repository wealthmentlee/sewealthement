<?php echo $this->content()->renderWidget('apptouch.admin-main-menu', array('active' => 'apptouch_admin_main_statistics')) ?>
<!--<h3>--><?php //echo $this->translate('TOUCH_Touch-Mobile Site Wide Statistics'); ?><!--</h3>-->
<div class="apptouch admin_home_right">
  <?php echo $this->content()->renderWidget('apptouch.admin-quick-menu', array('menu_name' => 'apptouch_admin_stats', 'active' => 'apptouch_admin_stats_general')); ?>
</div>
<div class="admin_home_middle">
  <?php echo $this->content()->renderWidget('apptouch.admin-statistics', array('show_as_chart' => true)) ?>
</div>
