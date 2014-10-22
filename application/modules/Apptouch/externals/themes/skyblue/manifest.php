<?php
return array(
  'package' => array(
    'type' => 'theme',
    'name' => 'skyblue',
    'version' => '4.2.0',
    'path' => 'application/modules/Apptouch/externals/themes/skyblue',
    'repository' => 'hire-experts.com',
    'title' => 'Sky Blue',
    'thumb' => 'skyblue.png',
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
      'application/modules/Apptouch/externals/themes/skyblue',
    ),
  ),
) ?>