<?php defined('_ENGINE') or die('Access Denied'); return array (
  'adapter' => 'mysqli',
  'params' => 
  array (
    'host' => 'wealthmentprod.c1fku54iim6w.us-east-1.rds.amazonaws.com',
    'username' => 'dev',
    'password' => 'wealthment',
    'dbname' => 'dev',
    'charset' => 'UTF8',
    'adapterNamespace' => 'Zend_Db_Adapter',
  ),
  'isDefaultTableAdapter' => true,
  'tablePrefix' => 'engine4_',
  'tableAdapterClass' => 'Engine_Db_Table',
); ?>
