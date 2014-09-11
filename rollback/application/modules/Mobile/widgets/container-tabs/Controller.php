<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_Widget_ContainerTabsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // Set up element
    $element = $this->getElement();
    $element->clearDecorators()
      //->addDecorator('Children', array('placement' => 'APPEND'))
      ->addDecorator('Container');

    // If there is action_id make the activity_feed tab active
    $action_id = (int) Zend_Controller_Front::getInstance()->getRequest()->getParam('action_id');
    $activeTab = $action_id ? 'mobile.activity-feed' : $this->_getParam('tab');

    if( empty($activeTab) ) {
      $activeTab = Zend_Controller_Front::getInstance()->getRequest()->getParam('tab');
    }

    // Iterate over children
    $tabs = array();
    $childrenContent = '';
    foreach( $element->getElements() as $child ) {

      // First tab is active if none supplied
      if( null === $activeTab ) {
        $activeTab = $child->getIdentity();
      }
      // If not active, set to display none
      if( $child->getIdentity() != $activeTab && $child->getName() != $activeTab) {
        $child->getDecorator('Container')->setParam('style', 'display:none;');
      }
      // Set specific class name
      $child_class = $child->getDecorator('Container')->getParam('class');
      $child->getDecorator('Container')->setParam('class', $child_class . ' tab_'.$child->getIdentity());

      // Remove title decorator
      $child->removeDecorator('Title');

      if( $child->getIdentity() == $activeTab ||  $child->getName() == $activeTab ) {
        // Render to check if it actually renders or not
        $childrenContent .= $child->render() . PHP_EOL;
        $activeTab = $child->getIdentity();
      } else {
        $child->render();
      }

      // Get title and childcount
      $title = $child->getTitle();
      $childCount = null;
      if( method_exists($child, 'getWidget') && method_exists($child->getWidget(), 'getChildCount') ) {
        $childCount = $child->getWidget()->getChildCount();
      }
      if( !$title ) $title = $child->getName();
      // If it does render, add it to the tab list

      $widget_url = false;
      if (method_exists($child->getWidget(), 'getHref')){
        $widget_url = $child->getWidget()->getHref();
      }

      if( !$child->getNoRender() ) {
        $tabs[] = array(
          'id' => $child->getIdentity(),
          'name' => $child->getName(),
          'containerClass' => $child->getDecorator('Container')->getClass(),
          'title' => $title,
          'childCount' => $childCount,
          'widget_url' => $widget_url
        );
      }
    }

    // Don't bother rendering if there are no tabs to show
    if( empty($tabs) ) {
      return $this->setNoRender();
    }
    $this->view->activeTab = $activeTab;
    $this->view->tabs = $tabs;
    $this->view->childrenContent = $childrenContent;
    $this->view->max =  $this->_getParam('max');
  }
}