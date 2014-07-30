<?php defined('_ENGINE') or die('Access Denied'); return array (
  'class' => 'Zend_Mail_Transport_Smtp',
  'args' => 
  array (
    0 => 'smtp.gmail.com',
    1 => 
    array (
      'port' => 465,
      'ssl' => 'ssl',
      'auth' => 'login',
      'username' => 'wealthment',
      'password' => 'wealthm123',
    ),
  ),
); ?>