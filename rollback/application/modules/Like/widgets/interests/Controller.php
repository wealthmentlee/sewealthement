<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Widget_InterestsController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    if (Engine_Api::_()->core()->hasSubject()) {
  	  $this->view->subject = $subject = Engine_Api::_()->core()->getSubject();
    }

		$this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

		if (!empty($subject) && $subject->getType() == 'user') {
			$this->view->isSelf = $viewer->isSelf($subject);
			$this->view->moduleApi = Engine_Api::_()->getDbTable('modules', 'core');

			$api = Engine_Api::_()->like();
  			$this->view->labels = $api->getInterestTypes();
			$this->view->icons = $api->getInterestIcons();

			$interests = array_keys($this->view->labels);
      $manifest = Zend_Registry::get('Engine_Manifest');
      $itemTypes = array();
      foreach($manifest as $man) {
        if (!empty($man['items'])) {
          foreach($man['items'] as $item) {
            $itemTypes[] = $item;
          }
        }
      }
      $interests = array_intersect($interests, $itemTypes);
      $items = $api->getLikedItems($subject,true);
      $params = array('poster_type' => $subject->getType(), 'poster_id' => $subject->getIdentity(), 'resource_types' => $interests);
      $fake_likes = $api->getFakeLikes($params);

      $this->view->items = $items;

      $fake_items = array();
      foreach($fake_likes as $fake_like)
      {
        $fake_items[$fake_like['resource_type']][] = $fake_like;
      }
      $this->view->fake_items = $fake_items;

			$this->view->showInterests = $subject->authorization()->isAllowed($viewer, 'interest');
		}
		// Don't render this if not authorized
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !Engine_Api::_()->core()->hasSubject() ) {
      return $this->setNoRender();
    }

    // Get subject and check auth
    $subject = Engine_Api::_()->core()->getSubject('user');
    if( !$subject->authorization()->isAllowed($viewer, 'view') ) {
      return $this->setNoRender();
    }

    // Load fields view helpers
    $view = $this->view;
    $view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    // Values
    $this->view->fieldStructure = $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
    if( count($fieldStructure) <= 1 ) { // @todo figure out right logic
      return $this->setNoRender();
    }

    return;

    $valuesStructure = array();
    $valueCount = 0;
    foreach( $fieldStructure as $index => $field )
    {
      $value = $field->getValue($subject);
      if( !$field->display )
      {
        continue;
      }

      if( $field->isHeading() )
      {
        $valuesStructure[] = array(
          'alias' => null,
          'label' => $field->label,
          'value' => $field->label,
          'heading' => true,
          'type' => $field->type,
        );
      }

      else if( $value && !empty($value->value) )
      {
        $valueCount++;

        $label = Engine_Api::_()->fields()
                 ->getFieldsOptions($subject)
                 ->getRowMatching('option_id', $value->value);
        $label = $label
                 ? $label->label
                 : $value->value;

        $valuesStructure[] = array(
          'alias' => $field->alias,
          'label' => $field->label,
          'value' => $label,
          'heading' => false,
          'type' => $field->type,
        );
      }
    }
    $this->view->user   = $subject;
    $this->view->fields = $valuesStructure;
    $this->view->valueCount = $valueCount;


    // Do not render if nothing to show
    if( $valueCount <= 0 ) {
      return $this->setNoRender();
    }
  }
  
}