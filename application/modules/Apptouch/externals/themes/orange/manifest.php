<?php
return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'orange',
    'version' => '4.2.0',
    'path' => 'application/modules/Apptouch/externals/themes/orange',
    'repository' => 'hire-experts.com',
    'title' => 'Orange',
    'thumb' => 'orange.png',
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
      'application/modules/Apptouch/externals/themes/orange',
    ),
  ),
) ?>