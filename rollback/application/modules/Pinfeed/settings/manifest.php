<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'pinfeed',
    'version' => '4.5.0',
    'path' => 'application/modules/Pinfeed',
    'title' => 'Pinfeed',
    'description' => 'Pinfeed',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.7',
      ),
      array(
        'type' => 'module',
        'name' => 'wall',
        'minVersion' => '4.2.6p4',
      ),
    ),
    'callback' =>
    array(
      'class' => 'Pinfeed_Installer',
      'path' => 'application/modules/Pinfeed/settings/install.php',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Pinfeed',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/pinfeed.csv',
    ),
  ),
  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Timeline - General
    'pinfeed' => array(
      'route' => 'pinfeed/:controller/:action/*',
      'defaults' => array(
        'module' => 'pinfeed',
        'controller' => 'index',
        'action' => 'index'
      ),

    ),

  ),
);
?>