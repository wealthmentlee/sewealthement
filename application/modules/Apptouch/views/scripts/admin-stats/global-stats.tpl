<?php
$day = (int)date('d');
$month = $day >= 15 ? (int)date('m') : ((int)date('m')) - 1;
$year = (int)date('Y');
$from_date = ($year - 1) . date('m');
$to_date = $year . date('m');
$period = $from_date . '-' . $to_date;
$type = 'bar';
?>
<?php echo $this->content()->renderWidget('apptouch.admin-main-menu', array('active' => 'apptouch_admin_main_statistics')) ?>

<div class="apptouch admin_home_right">
  <?php echo $this->content()->renderWidget('apptouch.admin-quick-menu', array('menu_name' => 'apptouch_admin_stats', 'active' => 'apptouch_admin_stats_global')); ?>
</div>
<div class="admin_home_middle">
  <div id="mobile_browser-ww-monthly-<?php echo $period ?>-<?php echo $type ?>" width="600" height="400"
       style="width:600px; height: 400px;">

  </div>
  <!-- You may change the values of width and height above to resize the chart -->
  <p>Source:
    <a href="http://gs.statcounter.com/#mobile_browser-ww-monthly-<?php echo $period ?>-<?php echo $type ?>">StatCounter
      Global Stats - Mobile Browser Market Share</a>
  </p>
  <script type="text/javascript" src="http://www.statcounter.com/js/FusionCharts.js"></script>
  <script type="text/javascript"
          src="http://gs.statcounter.com/chart.php?mobile_browser-ww-monthly-<?php echo $period ?>-<?php echo $type ?>"></script>
</div>
