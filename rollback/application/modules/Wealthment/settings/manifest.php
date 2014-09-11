<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'wealthment',
    'version' => '4.0.0',
    'path' => 'application/modules/Wealthment',
    'title' => 'Wealthment',
    'description' => 'Everything about wealthment customization',
    'author' => 'Gitesh Dang (ibytetechnologies.com)',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
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
      0 => 'application/modules/Wealthment',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/wealthment.csv',
    ),
  ),
); ?>