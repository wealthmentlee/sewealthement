<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'advancedsearch',
    'version' => '4.5.1p6',
    'path' => 'application/modules/Advancedsearch',
    'title' => 'Advanced Search',
    'description' => 'Advanced Search from Hire-Experts LLC',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' =>
      array (
        'title' => 'Advanced Search Plugin',
        'description' => 'Hire-Experts Advanced Search Plugin',
        'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
      ),
    'callback' => 
    array (
      'path' => 'application/modules/Advancedsearch/settings/install.php',
      'class' => 'Advancedsearch_Installer',
    ),
    'dependencies' => array(
      array(
        'type' => 'module',
        'name' => 'hecore',
        'minVersion' => '4.2.0p9',
      )
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
      0 => 'application/modules/Advancedsearch',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/advancedsearch.csv',
    ),
  ),
  'hooks' => array(
    array(
      'event' => 'onRenderLayoutDefault',
      'resource' => 'Advancedsearch_Plugin_Core',
    ),
  ),
  'routes' => array(
    'advancedsearch' => array(
      'route' => 'search/:action/*',
      'defaults' => array(
        'module' => 'advancedsearch',
        'controller' => 'index',
        'action' => 'index',
      ),
      'reqs' => array(
        'controller' => '\D+',
        'action' => '\D+',
      )
    ),
  )
); ?>