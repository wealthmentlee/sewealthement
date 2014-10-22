<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 04.07.12
 * Time: 15:54
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_Controller_Action_Helper_Fields
  extends Zend_Controller_Action_Helper_Abstract
{
  public function toArray($subject, $partialStructure = null)
  {
    $this->getActionController()->view->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');

    if (!($subject instanceof Core_Model_Item_Abstract) || !$subject->getIdentity()) {
      return '';
    }

    if (empty($partialStructure)) {
      $partialStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($subject);
    }

    // Generate
    $content = array();
    $lastContents = array();
    $lastHeadingTitle = null; //Zend_Registry::get('Zend_Translate')->_("Missing heading");

    $viewer = Engine_Api::_()->user()->getViewer();
    $show_hidden = $viewer->getIdentity()
      ? ($subject->getOwner()->isSelf($viewer) || 'admin' === Engine_Api::_()->getItem('authorization_level', $viewer->level_id)->type)
      : false;

    foreach ($partialStructure as $map) {

      // Get field meta object
      $field = $map->getChild();
      $value = $field->getValue($subject);
      if (!$field || $field->type == 'profile_type') continue;
      if (!$field->display && !$show_hidden) continue;

      // Heading
      if ($field->type == 'heading') {
        if (!empty($lastContents)) {
          $content[] = $this->_buildLastContents($lastContents, $lastHeadingTitle);
          $lastContents = '';
        }
        $lastHeadingTitle = $this->getActionController()->view->translate($field->label);
      }

      // Normal fields
      else
      {
        $tmp = $this->getFieldValueString($field, $value, $subject, $map, $partialStructure);
        if (!empty($tmp)) {

          $notice = !$field->display && $show_hidden
            ? sprintf('<div class="tip"><span>%s</span></div>',
              $this->getActionController()->view->translate('This field is hidden and only visible to you and admins:'))
            : '';
          $label = $this->getActionController()->view->translate($field->label);
          $lastContents[] = array(
            'notice' => $notice,
            'label' => $label,
            'value' => $tmp
          );
        }
      }

    }

    if (!empty($lastContents)) {
      $content[] = $this->_buildLastContents($lastContents, $lastHeadingTitle);
    }

    return $content;
  }

  public function getFieldValueString($field, $value, $subject, $map = null,
                                      $partialStructure = null)
  {
    if ((!is_object($value) || !isset($value->value)) && !is_array($value)) {
      return null;
    }

    // @todo This is not good practice:
    // if($field->type =='textarea'||$field->type=='about_me') $value->value = nl2br($value->value);

    $helperName = Engine_Api::_()->fields()->getFieldInfo($field->type, 'helper');
    if (!$helperName) {
      return null;
    }

    $helper = $this->getActionController()->view->getHelper($helperName);
    if (!$helper) {
      return null;
    }

    $helper->structure = $partialStructure;
    $helper->map = $map;
    $helper->field = $field;
    $helper->subject = $subject;
    try{
    $tmp = $helper->$helperName($subject, $field, $value);

    } catch (Exception $e){}
    unset($helper->structure);
    unset($helper->map);
    unset($helper->field);
    unset($helper->subject);

    return $tmp;
  }

  protected function _buildLastContents($content, $title)
  {
    if (!$title) {
      return $content;
    }
    return array(
      'title' => $title,
      'content' => $content
    );
  }

  public function direct()
  {
    return $this;
  }
}
