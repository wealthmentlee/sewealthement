<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Actors.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_Helper_Actors extends Activity_Model_Helper_Abstract
{
  public function direct($subject, $object, $separator = null)
  {
    if (!$separator){
      $separator = ' &rarr; ';
      $view = Zend_Registry::get('Zend_View');
      if ($view && $view->layout()->orientation == 'right-to-left'){
        $separator = ' &larr; ';
      }
    }

    $pageSubject = Engine_Api::_()->core()->hasSubject() ? Engine_Api::_()->core()->getSubject() : null;

    $subject = $this->_getItem($subject, false);
    $object = $this->_getItem($object, false);
    
    // Check to make sure we have an item
    if( !($subject instanceof Core_Model_Item_Abstract) || !($object instanceof Core_Model_Item_Abstract) )
    {
      return false;
    }

    if (Engine_Api::_()->wall()->isOwnerTeamMember($this->getAction()->getObject(), $this->getAction()->getSubject())){

      return $this->getAction()->getObject()->toString(array(
        'class' => 'feed_item_username wall_liketips',
        'rev' => $this->getAction()->getObject()->getGuid()
      ));
    }


    $subject_attribs = array('class' => 'feed_item_username hhe');

    if ($subject){
      $subject_attribs['class'] = 'feed_item_username wall_liketips';
      $subject_attribs['rev'] = $subject->getGuid();
    }


    $object_attribs = array('class' => 'feed_item_username hehe');

    if ($object){
      $object_attribs['class'] = 'feed_item_username wall_liketips';
      $object_attribs['rev'] = $object->getGuid();
    }

    if( null === $pageSubject ) {
      return $subject->toString($subject_attribs) . $separator . $object->toString($object_attribs);
    } else if( $pageSubject->isSelf($subject) ) {
      return $subject->toString($subject_attribs) . $separator . $object->toString($object_attribs);
    } else if( $pageSubject->isSelf($object) ) {
      return $subject->toString($subject_attribs);
    } else {
      return $subject->toString($subject_attribs) . $separator . $object->toString($object_attribs);
    }
  }
}
