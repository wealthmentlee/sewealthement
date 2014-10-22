<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8758 2011-03-30 23:50:30Z john $
 * @author     John
 */
?>

<h3 class="sep">
  <span>
    <?php echo $this->translate('Plugin Usage Stats') ?>
  </span>
</h3>
<?php if ($this->statistics) { ?>
<table class='admin_home_stats  `'>
  <thead>
  <tr>
    <th colspan='3' align="left"><?php echo $this->translate('Plugin Information') ?></th>
  </tr>
  </thead>
  <tbody>
  <tr>
    <td><?php echo $this->translate('Version') ?></td>
    <td colspan='2'><?php echo $this->apptouchVersion ?></td>
  </tr>
  <tr>
    <td><?php
        if(!$this->tablet)
          echo '<a href="http://www.hire-experts.com/social-engine/touch-tablet-plugin">'.$this->translate('APPTOUCH_Tablet Extension').'?</a>';
        else
          echo $this->translate('APPTOUCH_Tablet Extension') . ' ' . $this->tabletVersion ?></td>
    <td colspan='2'><?php
      if($this->tablet == 'installed'){
        echo '<span class="tablet_installed">' . $this->translate('APPTOUCH_Installed') . '</span>';
      } elseif($this->tablet == 'disabled'){
        echo '<span class="tablet_disabled">' . $this->translate('APPTOUCH_Disabled') . '</span>';
      } else {
        echo '<span class="tablet_not_installed">' . $this->translate('APPTOUCH_Not installed') . '</span>';
      }
      ?></td>
  </tr>
  <?php if(isset($this->app) && $this->app == 'installed'):?>
  <tr>
    <td><?php
        if(!$this->app)
          echo '<a href="http://www.hire-experts.com/social-engine-plugins">'.$this->translate('APPTOUCH_iPhone, iPad & Android Applications').'</a>';
        else
          echo $this->translate('APPTOUCH_Application Manager') . ' ' . $this->appVersion ?></td>
    <td colspan='2'><?php
      if($this->app == 'installed'){
        echo '<span class="app_installed">' . $this->translate('APPTOUCH_Installed') . '</span>';
      } elseif($this->app == 'disabled'){
        echo '<span class="app_disabled">' . $this->translate('APPTOUCH_Disabled') . '</span>';
      } else {
        echo '<span class="app_not_installed">' . $this->translate('APPTOUCH_Not installed') . '</span>';
      }
      ?></td>
  </tr>
      <?php endif; ?>
  </tbody>
</table>

<table class='admin_home_stats'>
  <thead>
  <tr>
    <th align="left"><?php echo $this->translate('Statistics') ?></th>
    <th align="left"><?php echo $this->translate('APPTOUCH_Via Mobile Devices') ?></th>
    <th align="left"><?php echo $this->translate('Total') ?></th>
  </tr>
  </thead>
  <tbody>
    <?php foreach ($this->statistics as $statistic): ?>
  <tr>
    <td>
      <?php echo $this->translate($statistic['label']) ?>
    </td>
    <td>
      <?php echo $this->locale()->toNumber($statistic['apptouch']) ?>
    </td>
    <td>
      <?php echo $this->locale()->toNumber($statistic['total']) ?>
    </td>
  </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php
} else {
  echo $this->content()->renderWidget('apptouch.admin-chart', array('chart_params' => $this->chart_params));
} ?>
