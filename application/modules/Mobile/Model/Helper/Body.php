<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Body.php 2011-02-21 11:13:46 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Model_Helper_Body extends Activity_Model_Helper_Abstract
{
  /**
   * Body helper
   * 
   * @param string $body
   * @return string
   */
  public function direct($body)
  {
    /** @var $request Zend_Controller_Request_Http */
    $request = Zend_Controller_Front::getInstance()->getRequest();

    $module = $request->getModuleName();
    $controller = $request->getControllerName();
    $action =  $request->getActionName();


    if( Zend_Registry::isRegistered('Zend_View') ) {
      $view = Zend_Registry::get('Zend_View');

      if ($module == 'activity' && $controller == 'index' && $action == 'view'){

      } else {

        if (Engine_String::strlen($body) > 200){

          $router = Zend_Controller_Front::getInstance()->getRouter();

          $view_href = $router->assemble(array(
            'module' => 'activity',
            'controller' => 'index',
            'action' => 'view',
            'action_id' => $this->getAction()->getIdentity()
          ), 'default');

          $view_more = '<a href="'.$view_href.'">' . Zend_Registry::get('Zend_Translate')->_('more') . '</a>';

          $body = $view->mobileSubstr($body, 200) . '  ' . $view_more;

        }

      }

    }
    return '<span class="feed_item_bodytext">' . $body . '</span>';
  }
}