<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: default.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">

<?php $locale = $this->locale()->getLocale()->__toString(); $orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>
  <base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />

  
  <?php // TITLE/META ?>
  <?php
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->headTitle()
      ->setSeparator(' - ');
    $this
      ->headTitle($this->layout()->siteinfo['title'], Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
      ;
    $this->headMeta()
      ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
      ->appendHttpEquiv('Content-Language', 'en-US');
    
    // Make description and keywords
    $description = '';
    $keywords = '';
    
    $description .= ' ' .$this->layout()->siteinfo['description'];
    $keywords = $this->layout()->siteinfo['keywords'];

    if( $this->subject() && $this->subject()->getIdentity() ) {
      $this->headTitle($this->subject()->getTitle());
      
      $description .= ' ' .$this->subject()->getDescription();
      if (!empty($keywords)) $keywords .= ',';
      $keywords .= $this->subject()->getKeywords(',');
    }
    
    $this->headMeta()->appendName('description', trim($description));
    $this->headMeta()->appendName('keywords', trim($keywords));
  ?>
  <?php echo $this->headTitle()->toString()."\n" ?>
  <?php echo $this->headMeta()->toString()."\n" ?>


  <?php // LINK/STYLES ?>
  <?php
    $this->headLink(array(
      'rel' => 'favicon',
      'href' => '/favicon.ico',
      'type' => 'image/x-icon'),
      'PREPEND');

    $themes = array();
    if( null !== ($theme = $this->mobileActiveTheme()) ) {
      $themes = array($theme->name);
    } else {
      $themes = array('default');
    }
  ?>
  <?php echo $this->headLink()->toString()."\n" ?>
  <?php echo $this->headStyle()->toString()."\n" ?>

	<style type="text/css">
		<?php echo $this->activeThemeStyles(); ?>
	</style>

</head>
<body id="global_page_<?php echo $request->getModuleName() . '-' . $request->getControllerName() . '-' . $request->getActionName() ?>" class="mobile-body">
  <div id="global_header">
    <?php echo $this->mobileContent('header') ?>
  </div>
  <div id='global_wrapper'>
    <div id='global_content'>
      <?php if(Zend_Controller_Front::getInstance()->getRequest()->getParam('not_mobile_integrated') && isset($this->viewer()->level_id) && $this->viewer()->level_id < 4){ ?>
      <div>
        <style type="text/css">
          div > span.not_mobile_integrated{
            background-color: #FBE3E4;
            border: 2px solid #FBC2C4;
            color: #D12F19;
            display: block;
            padding: 8px 5px;
          }
        </style>
        <span class="not_mobile_integrated">
          <?php echo $this->translate('MOBILE_NOT_INTEGRATED_PAGE') ?>
          <?php echo $this->translate('MOBILE_ADMINS_VIEW_ONLY_MESS') ?>
        </span>
      </div>
      <?php } ?>
      <?php echo $this->layout()->content ?>
    </div>
  </div>
  <div id="global_footer">
    <?php echo $this->mobileContent('footer') ?>
  </div>
</body>
</html>