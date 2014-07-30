<?php return array (
  'package' => 
  array (
    'type' => 'theme',
    'name' => 'Bright Blue',
    'version' => '4.1.4',
    'revision' => '$Revision: 7306 $',
    'path' => 'application/modules/Mobile/themes/brightblue',
    'repository' => 'socialengine.net',
    'meta' => 
    array (
      'title' => 'Bright Blue',
      'thumb' => 'brightblue_theme.jpg',
      'author' => 'Good Guy',
      'changeLog' => 
      array (
        '4.1.4' =>
        array (
          'constants.css' => 'Designed',
          'manifest.php' => 'Designed',
          'theme.css' => 'Designed',
        ),
      ),
      'name' => 'brightblue',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'remove',
    ),
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => 
    array (
      0 => 'application/modules/Mobile/themes/brightblue',
    ),
  ),
  'files' => 
  array (
    0 => 'theme.css',
    1 => 'constants.css',
  ),
); ?>