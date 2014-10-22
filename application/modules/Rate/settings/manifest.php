<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'rate',
    'version' => '4.1.7p5',
    'path' => 'application/modules/Rate',
    'title' => 'Rate',
    'description' => 'Rate Plugin',
    'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    'meta' => 
    array (
      'title' => 'Rate',
      'description' => 'Rate Plugin',
      'author' => '<a href="http://www.hire-experts.com" title="Hire-Experts LLC" target="_blank">Hire-Experts LLC</a>',
    ),
      'dependencies' => array(
          array(
              'type' => 'module',
              'name' => 'core',
              'minVersion' => '4.1.5',
          ),
          array(
              'type' => 'module',
              'name' => 'hecore',
              'minVersion' => '4.2.1p1',
          ),
      ),

      'callback' => array(
      'path' => 'application/modules/Rate/settings/install.php',
      'class' => 'Rate_Installer',
    ),
    'actions' => 
    array (
      'preinstall',
      'install',
      'upgrade',
      'refresh',
      'enable',
      'disable',
    ),

    // Content -------------------------------------------------------------------
    'content'=> array(
      'rate_widget' => array(
        'type' => 'action',
        'title' => 'Rate This',
        'route' => array(
          'module' => 'rate',
          'controller' => 'widget',
          'action' => 'widget-rate',
        ),
      )
    ),

    'directories' => 
    array (
      0 => 'application/modules/Rate',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/rate.csv',
    ),
  ),

  // Hooks ---------------------------------------------------------------------
  'hooks' => array(
    array(
      'event' => 'removePage',
      'resource' => 'Rate_Plugin_Core'
    ),
    array(
      'event' => 'onUserDeleteBefore',
      'resource' => 'Rate_Plugin_Core',
    ),
    array(
      'event' => 'typeDelete',
      'resource' => 'Rate_Plugin_Core'
    ),
    array(
      'event' => 'typeCreate',
      'resource' => 'Rate_Plugin_Core'
    )
  ),

  'items' => array(
    'pagereview',
    'offerreview'
  ),

  // Routes --------------------------------------------------------------------
  'routes' => array(
    // Public
    'getRateContainer' => array(
      'route' => 'rate/getratecontainer',
      'defaults' => array(
        'module' => 'rate',
        'controller' => 'index',
        'action' => 'getratecontainer',
      )
    ),

    // Public
    'widget_rate' => array(
      'route' => 'rate/rate',
      'defaults' => array(
        'module' => 'rate',
        'controller' => 'index',
        'action' => 'rate',
      )
    ),

    'rate_admin_level' => array(
      'route' => 'admin/rate/level/:id',
      'defaults' => array(
        'module' => 'rate',
        'controller' => 'admin-level',
        'action' => 'index'
      )
    ),

    'page_review' => array(
      'route' => 'page-review/:action/*',
      'defaults' => array(
        'module' => 'rate',
        'controller' => 'review',
        'action' => 'index',
      )
    ),

    'offer_review' => array(
      'route' => 'offer-review/:action/*',
      'defaults' => array(
        'module' => 'rate',
        'controller' => 'offer-review',
        'action' => 'index',
      )
    ),

    'browse_reviews' => array(
      'route' => 'browse-reviews/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'browsereviews',
      ),
    ),

    'browse_reviews_sort' => array(
      'route' => 'browse-reviews/*',
      'defaults' => array(
        'module' => 'page',
        'controller' => 'index',
        'action' => 'browsereviews',
      ),
    ),
  )
) ?>