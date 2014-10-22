<?php
return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'gray',
    'version' => '4.2.0',
    'path' => 'application/modules/Apptouch/externals/themes/gray',
    'repository' => 'hire-experts.com',
    'title' => 'Gray',
    'thumb' => 'gray.png',
    'author' => 'Hire-Experts LLC',
    'changeLog' => array(),
    'actions' => array(
      'install',
      'upgrade',
      'refresh',
      'remove',
    ),
    'callback' => array(
      'class' => 'Engine_Package_Installer_Theme',
    ),
    'directories' => array(
      'application/modules/Apptouch/externals/themes/gray',
    ),
  ),
) ?>