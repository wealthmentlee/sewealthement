<?php
class Pinfeed_Plugin_Core extends Zend_Controller_Plugin_Abstract
{
  public function routeShutdown(Zend_Controller_Request_Abstract $request)
  {

    if (!(Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('apptouch') &&
        Engine_Api::_()->apptouch()->isApptouchMode()) &&
      (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('touch') &&
        Engine_Api::_()->touch()->isTouchMode() ||
        Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('mobile') &&
        Engine_Api::_()->mobile()->isMobileMode())
    ) {
      return false;
    }


    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action = $request->getActionName();
//   print_arr($request->getParams());
    if($controller == 'admin-content' && $module == 'core' && $action == "index") {
//      print_die($request->getParams());
      $translate = Zend_Registry::get('Zend_Translate');
      $pagesTable = Engine_Api::_()->getDbTable('pages', 'core');
      $pagesSelect = $pagesTable->select('page_id')->where('name = ?','pinfeed_index_index');
      $codeid = $pagesTable->fetchRow($pagesSelect);
      $contentTable = Engine_Api::_()->getDbTable('content', 'core');
      $contentSelect = $contentTable->select('content_id')->where('page_id = ?',$codeid->page_id)->where('name=?','middle');
      $content_id = $contentTable->fetchRow($contentSelect);
      if($codeid->page_id == $request->getParam('page') && isset($content_id->content_id)){
        $check = $codeid->page_id;
        $headScript = new Zend_View_Helper_HeadScript();
        $headScript->appendScript("
        window.addEvent('load', function(){
        $('admin_content_".$content_id->content_id."').addClass('area-disabled');
         $$('#admin_content_".$content_id->content_id." .admin_content_sortable').setStyle('display', 'none');
         var elementHtml = new Element('<div>', {'html': '".$translate->translate('PIN_NOT_EDIT_ADMIN')."', 'id': 'pinfeed_messege'});
         elementHtml.inject($('admin_content_".$content_id->content_id."'),'top' )
        })
        ");

      }
     }




    /**
     * @var $settings Core_Api_Settings
     */
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if ($module == 'user' && $controller == 'index' && $action == 'home') {
      if(!Engine_Api::_()->hasModuleBootstrap('apptouch') || !Engine_Api::_()->apptouch()->isApptouchMode()){
      if ($settings->__get('Pinfeed.use_homepage', 'choice') == 1 ) {
        $request->setModuleName('pinfeed')->setActionName('index');
        return;
      }
      }
//|| $settings->__get('pinfeed.usage', 'choice') == 'force'
/*      $id = $request->getParam('id');

      $user = Engine_Api::_()->user()->getUser($id);
      if ($user->getIdentity()) {
        $user = Engine_Api::_()->getDbTable('users', 'timeline')->findRow($user->getIdentity());
      }*/

/*      if (true || $user->getIdentity() && Engine_Api::_()->getDbTable('settings', 'user')->getSetting($user, 'timeline-usage')) {
        $request->setModuleName('pinfeed');
        return;
      }*/
    }
  }
}
?>