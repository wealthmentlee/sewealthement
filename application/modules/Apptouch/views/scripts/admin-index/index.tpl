<?php echo $this->content()->renderWidget('apptouch.admin-main-menu', array('active' => 'apptouch_admin_main_home')) ?>

<div class="admin_home_wrapper">

  <div class="admin_home_right">
    <?php echo $this->content()->renderWidget('apptouch.admin-statistics') ?>
  </div>

  <div class="admin_home_middle">
    <p>
      <?php echo $this->translate('APPTOUCH_ADMIN_DASHBOARD'); ?>
    </p>
    <br/>
    <?php echo $this->content()->renderWidget('apptouch.admin-dashboard') ?>
    <?php echo $this->content()->renderWidget('apptouch.admin-iphone-simulator') ?>
  </div>

</div>
