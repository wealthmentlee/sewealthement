<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN">

<?php
//  $arr = Zend_Json::decode($this->jsonInline($this->content));
//  echo($arr['html']);
$locale = $this->locale()->getLocale()->__toString(); $orientation = ($this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr'); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>"
      dir="<?php echo $orientation ?>"
  >
<head>
  <title><?php echo @$this->page['info']['title'] ?></title>
  <meta content="width=device-width; initial-scale=1.0; maximum-scale=1.0; minimum-scale=1.0; user-scalable=0;" name="viewport">
  <?php if($this->homeScreen()) echo $this->homeScreen()->render(); ?>
  <meta content="<?php echo APPLICATION_ENV ?>" name="app_env">
  <meta content="<?php echo $this->isMaintenanceMode(); ?>" name="is_maintenance">
  <base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/') . '/' ?>"/>

  <?php // LINK/STYLES ?>
  <?php
  $this->headMeta()
			->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
      ->appendHttpEquiv('Content-Language', $this->locale()->getLocale()->__toString());

  $staticBaseUrl = $this->layout()->staticBaseUrl;

  echo $this->apptouch()->css();
//  ->prependStylesheet($staticBaseUrl . 'application/modules/Apptouch/externals/styles/jquery.mobile-1.2.0.css');

//  Enable Theme {
  echo $this->theme();
//  } Enable Theme
  // Tablet css files
  if (Engine_Api::_()->apptouch()->isTabletMode()){
    echo $this->apptablet()->css();
  }
  echo $this->headMeta()->toString()."\n";
  ?>

</head>
<?php
$request = Zend_Controller_Front::getInstance()->getRequest();
?>

<body
  id="global_page_<?php echo $request->getModuleName() . '-' . $request->getControllerName() . '-' . $request->getActionName() ?>"
  class="apptouch-body <?php if (Engine_Api::_()->apptouch()->isTabletMode()){ ?>tablet<?php } else { ?> phone <?php } ?>">
<?php echo $this->localeFormats()->render();
$baseUrl = $this->baseUrl();
?>
<style>
@font-face {
  font-family: 'FontIcon';
  src: url('<?php echo $baseUrl ?>/application/modules/Apptouch/externals/fonts/icons.eot');
  src: url('<?php echo $baseUrl ?>/application/modules/Apptouch/externals/fonts/icons.eot#iefix') format('embedded-opentype'),
    url('<?php echo $baseUrl ?>/application/modules/Apptouch/externals/fonts/icons.woff') format('woff'),
    url('<?php echo $baseUrl ?>/application/modules/Apptouch/externals/fonts/icons.ttf') format('truetype');
  font-weight: normal;
  font-style: normal;
}
</style>
<div id="initial_page" data-role="page" data-theme="a" data-url="initial_page">

	<img class="site-logo" src="<?php echo $this->siteLogo()->url() ?>" />
	<h1 class="site-title"><?php $title = $this->layout()->siteinfo['title']; 
echo $this->translate('' . (is_array($title) ? $title[Zend_Registry::get('Locale')->getLanguage()] : $title)) ?></h1>
  <span class="site-description"><?php $desc = $this->layout()->siteinfo['description']; echo is_array($desc) ? $desc[Zend_Registry::get('Locale')->getLanguage()] : $desc?></span>
	<h4><?php echo $this->translate('Copyright &copy;%s', date('Y')) ?></h4>
</div><!-- /page -->
<?php
$this->addScriptPath('application/modules/Apptouch/views/scripts');

?>
<!-- Like, Unlike effect Elements on the Wall { -->
<span class="ui-icon-like-up buttonAnimate" style="display: none;"></span>
<span class="ui-icon-unlike buttonAnimate" style="display: none;"></span>
<!-- } Like, Unlike effect Elements on the Wall -->

<?php
$isDev = APPLICATION_ENV == 'development' ||
  Engine_Api::_()->getDbTable('settings', 'core')->getSetting('apptouch.use.dev.scripts', false);  // HOSTISO & CLOUD-FLARE EXCEPTION$envDir =  $isDev ? 'dev/' : '';
// SCRIPTS
if(array_key_exists('HTTP_USER_AGENT', $_SERVER) && preg_match("/trident\/(4|3)/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/' . $envDir . 'ie.js');

echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/' . $envDir . 'picup.js');
if(true){
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/jquery.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/jquery.mobile-1.2.0.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/eikooc.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/photoswipe/lib/klass.min.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/photoswipe/code.photoswipe.jquery-3.0.4.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/jqmWidgets.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/core.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/initializers.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/components.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/activity.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/iscroll.js');
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/dev/chat.js');
}else
  echo $this->apptouchScript($baseUrl . '/application/modules/Apptouch/externals/scripts/js.js');

echo $this->lang()->add(array(
  'Page Not Found',
  'Go Back',
  'APPTOUCH_Upload Success',
  'APPTOUCH_Ooops! Sorry, something went wrong...',
  'APPTOUCH_Please enable pop-up windows for this site and try again.',
  '%s view',
  '%1$s of %2$s',
  'APPTOUCH_Detecting faces...',
  'APPTOUCH_Swipe to clear hashtag filter'
))->toString();

echo $this->templates()->render();

// Tablet js files
if (Engine_Api::_()->apptouch()->isTabletMode()){
  echo $this->apptablet()->scripts();
}

  $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
  echo $this->apptouchScript($prefix.'maps.google.com/maps/api/js?sensor=true');
  echo $this->apptouchScript($prefix.'maps.googleapis.com/maps/api/js?sensor=false&libraries=places');
?>

<script data-cfasync="false" type="text/javascript">
  $(document).bind("ready", function () {
    core.construct(<?php echo @$this->jsonInline($this->getVars()); ?>);
  });
  core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');
</script>

</body>
</html>