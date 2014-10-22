<?php

class Apptouch_View_Helper_Apptouch extends Zend_View_Helper_Abstract
{

  public function apptouch()
  {
    return $this;
  }

    public function scripts()
    {
  //    $staticBaseUrl = $this->view->layout()->staticBaseUrl;

      return  <<<CONTENT
CONTENT;

    }

    public function css()
    {
      $staticBaseUrl = $this->view->layout()->staticBaseUrl;
      $isDev = APPLICATION_ENV == 'development'/* ||
          strpos($_SERVER['SERVER_ADDR'], '5.9.')*/;
      if(true ||$isDev) {
        $noCache = '?nocache=' . rand(0, 1000);
        $css =  <<<CONTENT
          <link href="{$staticBaseUrl}application/modules/Apptouch/externals/styles/jqm-icon-pack-2.0-original.css{$noCache}" media="screen" rel="stylesheet" type="text/css"/>
          <link href="{$staticBaseUrl}application/modules/Apptouch/externals/styles/jquery.ui.datepicker.mobile.css{$noCache}" media="screen" rel="stylesheet" type="text/css"/>
          <link href="{$staticBaseUrl}application/modules/Apptouch/externals/styles/photoswipe.css{$noCache}" media="screen" rel="stylesheet" type="text/css"/>
          <link href="{$staticBaseUrl}application/modules/Apptouch/externals/styles/core.css{$noCache}" media="screen" rel="stylesheet" type="text/css"/>
          <link href="{$staticBaseUrl}application/modules/Apptouch/externals/styles/components.css{$noCache}" media="screen" rel="stylesheet" type="text/css"/>
          <link href="{$staticBaseUrl}application/modules/Apptouch/externals/styles/custom.css{$noCache}" media="screen" rel="stylesheet" type="text/css"/>
CONTENT;
      }
      else
        $css =  '<link href="'.$staticBaseUrl.'application/modules/Apptouch/externals/styles/css.css" media="screen" rel="stylesheet" type="text/css"/>';
      return $css;
    }
}
