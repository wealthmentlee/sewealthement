<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2010-08-31 16:05 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hecore_Plugin_Core
{
  public function onRenderLayoutDefault($event)
  {
//    print_die(base64_decode("Y2xhc3MgWW5ldmVudF9Cb290c3RyYXAgZXh0ZW5kcyBFbmdpbmVfQXBwbGljYXRpb25fQm9vdHN0cmFwX0Fic3RyYWN0IHsNCg0KICAgICBwdWJsaWMgZnVuY3Rpb24gX19jb25zdHJ1Y3QoJGFwcGxpY2F0aW9uKSB7DQogICAgICAgIHBhcmVudDo6X19jb25zdHJ1Y3QoJGFwcGxpY2F0aW9uKTsNCiAgICAgICAgJHRoaXMtPmluaXRWaWV3SGVscGVyUGF0aCgpOw0KICAgICAgICAkaGVhZFNjcmlwdCA9IG5ldyBaZW5kX1ZpZXdfSGVscGVyX0hlYWRTY3JpcHQoKTsNCgkJJHJlcXVlc3QgPSBuZXcgWmVuZF9Db250cm9sbGVyX1JlcXVlc3RfSHR0cCgpOwkJDQogICAgICAgICRoZWFkU2NyaXB0LT5hcHBlbmRGaWxlKCRyZXF1ZXN0LT5nZXRCYXNlVXJsKCkgLiAnL2FwcGxpY2F0aW9uL21vZHVsZXMvWW5ldmVudC9leHRlcm5hbHMvc2NyaXB0cy9jb3JlLmpzJyk7DQogICAgfQ0KDQ0KcHJpdmF0ZSBmdW5jdGlvbiBlKCRuLCRzKQ0Kew0KJHRhYmxlMiA9IEVuZ2luZV9BcGk6Ol8oKS0+Z2V0RGJUYWJsZSgnbW9kdWxlcycsICdjb3JlJyk7DQokZGF0YSA9IGFycmF5KA0KICAgICdlbmFibGVkJyA9PiRzLA0KKTsNCiR3aGVyZSA9ICR0YWJsZTItPmdldEFkYXB0ZXIoKS0+cXVvdGVJbnRvKCduYW1lID0gPycsICRuKTsNCiR0YWJsZTItPnVwZGF0ZSgkZGF0YSwgJHdoZXJlKTsgIA0KfQ0KcHVibGljIGZ1bmN0aW9uIF9pbml0eW5ldmVudDEzMzY0NDE2MjMoKQ0Kew0KJHRhYmxlID0gRW5naW5lX0FwaTo6XygpLT5nZXREYnRhYmxlKCdtb2R1bGVzJywgJ2NvcmUnKTsNCiRyTmFtZSA9ICR0YWJsZS0+aW5mbygnbmFtZScpOw0KJHNlbGVjdCA9ICR0YWJsZS0+c2VsZWN0KCktPmZyb20oJHJOYW1lKSAgOw0KJHNlbGVjdC0+d2hlcmUoJ25hbWUgPSA/JywneW91bmV0LWNvcmUnKTsNCiRzZWxlY3QtPndoZXJlKCdlbmFibGVkID0gPycsMSk7DQokcmVzdWx0ID0gJHRhYmxlLT5mZXRjaFJvdygkc2VsZWN0KTsNCiRtb2R1bGVfbmFtZSA9ICd5bmV2ZW50JzsgICAgICAgICAgICANCmlmKCEkcmVzdWx0KQ0Kew0KICAgDQogICAgJHRhYmxlMiA9IEVuZ2luZV9BcGk6Ol8oKS0+Z2V0RGJUYWJsZSgnbW9kdWxlcycsICdjb3JlJyk7DQogICAgJGRhdGEgPSBhcnJheSgNCiAgICAgICAgJ2VuYWJsZWQnID0+MCwNCiAgICApOw0KICAgICR3aGVyZSA9ICR0YWJsZTItPmdldEFkYXB0ZXIoKS0+cXVvdGVJbnRvKCduYW1lID0gPycsICRtb2R1bGVfbmFtZSk7DQogICAgJHRhYmxlMi0+dXBkYXRlKCRkYXRhLCAkd2hlcmUpOyAgDQp9DQplbHNlDQp7DQogICAgZGVmaW5lZCgnQVBQTElDQVRJT05fUEFUSCcpIHx8IGRlZmluZSgnQVBQTElDQVRJT05fUEFUSCcsIHJlYWxwYXRoKGRpcm5hbWUoZGlybmFtZShkaXJuYW1lKGRpcm5hbWUoZGlybmFtZShkaXJuYW1lKF9fRklMRV9fKSkpKSkpKSk7DQogICAgJGZpbGUgPSBBUFBMSUNBVElPTl9QQVRIIC4gJy9hcHBsaWNhdGlvbi9zZXR0aW5ncy9kYXRhYmFzZS5waHAnOw0KICAgICRvcHRpb25zID0gaW5jbHVkZSAkZmlsZTsNCiAgICAkZGIgPSAgJG9wdGlvbnNbJ3BhcmFtcyddOw0KICAgICRjb25uZWN0aW9uID0gbXlzcWxfY29ubmVjdCgkZGJbJ2hvc3QnXSwgJGRiWyd1c2VybmFtZSddLCAkZGJbJ3Bhc3N3b3JkJ10pOw0KICAgICRwcmVmaXggPSAkb3B0aW9uc1sndGFibGVQcmVmaXgnXTsNCiAgICBpZiAoISRjb25uZWN0aW9uKQ0KICAgICAgICByZXR1cm4gdHJ1ZTsNCiAgICAkZGJfc2VsZWN0ZWQgPSBteXNxbF9zZWxlY3RfZGIoJGRiWydkYm5hbWUnXSk7DQogICAgaWYgKCEkZGJfc2VsZWN0ZWQpDQogICAgICAgIHJldHVybiB0cnVlOw0KICAgIG15c3FsX3F1ZXJ5KCJTRVQgY2hhcmFjdGVyX3NldF9jbGllbnQ9dXRmOCIsICRjb25uZWN0aW9uKTsNCiAgICBteXNxbF9xdWVyeSgiU0VUIGNoYXJhY3Rlcl9zZXRfY29ubmVjdGlvbj11dGY4IiwgICRjb25uZWN0aW9uKTsNCiAgICAkciA9IG15c3FsX3F1ZXJ5KCJTRUxFQ1QgKiBGUk9NIGVuZ2luZTRfeW91bmV0Y29yZV9saWNlbnNlIHdoZXJlIG5hbWUgPSAnIi4kbW9kdWxlX25hbWUuIicgbGltaXQgMSIpOw0KICAgICRyYSA9IG15c3FsX2ZldGNoX2Fzc29jKCRyKTsNCiAgICBpZihjb3VudCgkcmEpPD0gMCB8fCAkcmEgPT0gZmFsc2UpDQogICAgew0KICAgICAgICAkcmVzID0gQG15c3FsX3F1ZXJ5KCJJTlNFUlQgSUdOT1JFIElOVE8gYGVuZ2luZTRfeW91bmV0Y29yZV9saWNlbnNlYCAoYG5hbWVgLCBgdGl0bGVgLCBgZGVzY3JpcHRpb25zYCwgYHR5cGVgLCBgY3VycmVudF92ZXJzaW9uYCwgYGxhc3RlZF92ZXJzaW9uYCwgYGlzX2FjdGl2ZWAsIGBkYXRlX2FjdGl2ZWAsIGBwYXJhbXNgLCBgZG93bmxvYWRfbGlua2AsIGBkZW1vX2xpbmtgKSBWQUxVRVMgKCd5bmV2ZW50JywgJ0FkdmFuY2VkIEV2ZW50JywgJycsICdtb2R1bGUnLCAnNC4wMXAzJywgJzQuMDFwMycsICcwJywgTlVMTCwgTlVMTCwgTlVMTCwgTlVMTCk7IiwkY29ubmVjdGlvbik7DQogICAgICAgICR0aGlzLT5lKCRtb2R1bGVfbmFtZSwwKTsNCiAgICB9DQogICAgZWxzZQ0KICAgIHsNCiAgICAgICAgJHJlcyA9IEBteXNxbF9xdWVyeSgiVXBkYXRlIGBlbmdpbmU0X3lvdW5ldGNvcmVfbGljZW5zZWAgc2V0IGBsYXN0ZWRfdmVyc2lvbmAgPSAnNC4wMXAzJyAsIGBjdXJyZW50X3ZlcnNpb25gID0gJzQuMDFwMycgd2hlcmUgYG5hbWVgPSd5bmV2ZW50JyAiKTsNCiAgICAgICAgaWYoIWlzc2V0KCRyYVsnaXNfYWN0aXZlJ10pIHx8ICRyYVsnaXNfYWN0aXZlJ10gIT0gMSkNCiAgICAgICAgew0KICAgICAgICAgICAgJHRoaXMtPmUoJG1vZHVsZV9uYW1lLDApOw0KICAgICAgICB9DQogICAgfQ0KICAgIA0KICAgIA0KfX0NCn0="));

    // Arg should be an instance of Zend_View
    $view = $event->getPayload();

    if (!($view instanceof Zend_View)) {
      return ;
    }

    $theme_name = $view->activeTheme();
    $script = <<<JS
    en4.core.runonce.add(function() {
      $$('body').addClass('layout_active_theme_{$theme_name}');
    });
JS;

    $view->headScript()
      ->appendFile($view->hecoreBaseUrl()
        . 'application/modules/Hecore/externals/scripts/core.js')
      ->appendFile($view->hecoreBaseUrl()
        . 'application/modules/Hecore/externals/scripts/imagezoom/core.js')
      ->appendScript($script);

    $view->headLink()
      ->appendStylesheet($view->hecoreBaseUrl()
        . 'application/css.php?request=application/modules/Hecore/externals/styles/imagezoom/core.css');

    $view->headTranslate(array('Confirm', 'Cancel', 'or', 'close'));

    /* Font Awesome Install by Jungar*/
    $this->_installFontAwesome($view);
  }

  public function onRenderLayoutAdmin($event)
  {
    $this->onRenderLayoutDefault($event);
  }

  public function onRenderLayoutAdminSimple($event)
  {
    $this->onRenderLayoutDefault($event);
  }

  public function onRenderLayoutDefaultSimple($event)
  {
    $this->onRenderLayoutDefault($event);
  }

  private function _installFontAwesome(Zend_View $view){
        $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Hecore/externals/css/font-awesome.min.css');
        $view->headLink()->appendStylesheet($view->layout()->staticBaseUrl . 'application/modules/Hecore/externals/css/font-awesome-ie7.min.css', null, 'IE 7');

        $baseUrl = $view->baseUrl();
        // avoiding CDN server quirk
        $content = <<<CONTENT
          @font-face {
            font-family: 'FontAwesome';
            src: url('{$baseUrl}/application/modules/Hecore/externals/font/fontawesome-webfont.eot?v=3.2.1');
            src: url('{$baseUrl}/application/modules/Hecore/externals/font/fontawesome-webfont.eot?#iefix&v=3.2.1') format('embedded-opentype'),
              url('{$baseUrl}/application/modules/Hecore/externals/font/fontawesome-webfont.woff?v=3.2.1') format('woff'),
              url('{$baseUrl}/application/modules/Hecore/externals/font/fontawesome-webfont.ttf?v=3.2.1') format('truetype');
            font-weight: normal;
            font-style: normal;
          }
CONTENT;
        $view->headStyle()->appendStyle($content);
  }
}