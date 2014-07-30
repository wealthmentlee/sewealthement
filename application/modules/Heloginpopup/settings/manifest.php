<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'heloginpopup',
    'version' => '4.5.0p2',
    'path' => 'application/modules/Heloginpopup',
    'repository' => 'hire-experts.com',
    'title' => 'HE - Loginpopup',
    'description' => 'Hire-Experts Loginpopup Plugin',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' =>
    array (
      'title' => 'HE - Loginpopup',
      'description' => 'Hire-Experts Loginpopup Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'core',
        'minVersion' => '4.1.8',
      ),
    ),
    'actions' =>
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'callback' => array(
      'path' => 'application/modules/Heloginpopup/settings/install.php',
      'class' => 'Heloginpopup_Installer',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Heloginpopup',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/heloginpopup.csv',
    ),
  ),

  // Hooks ---------------------------------------------------------------------

  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Heloginpopup_Plugin_Core',
    ),
  ),
); ?>