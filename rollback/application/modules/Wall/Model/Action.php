<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Action.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_Action extends Activity_Model_Action
{
  public $grouped_subjects = array();


  public function getView()
  {
    return Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
  }

  public function getContent()
  {
    $model = Engine_Api::_()->getApi('core', 'wall');
    $params = array_merge(
      $this->toArray(),
      (array) $this->params,
      array(
        'subject' => $this->getSubject(),
        'object' => $this->getObject(),
      )
    );
    //$content = $model->assemble($this->body, $params);
    $content = $model->assemble($this->getTypeInfo()->body, $params, $this);


    return $content;
  }

  public function getType()
  {
    return 'activity_action';
  }

  public function getCheckin()
  {
    if (!Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('checkin')) {
      return 0;
    }

    return Engine_Api::_()->getDbTable('checks', 'checkin')->getActionById($this->action_id);
  }

  public function hasObjectItem()
  {
    return Engine_Api::_()->hasItemType($this->object_type);
  }

  public function canChangePrivacy($viewer)
  {
    if (!$viewer){
      return ;
    }
    return (('user' == $this->subject_type && $viewer->getIdentity() == $this->subject_id));
  }


  public function changePrivacy($privacy)
  {
    $privacy_type = $this->object_type;
    $privacy_list = Engine_Api::_()->wall()->getPrivacy($privacy_type);

    if (empty($privacy_list)){
      return ;
    }
    if (!in_array($privacy, $privacy_list)){
      return ;
    }

    $privacyTable = Engine_Api::_()->getDbTable('privacy', 'wall');
    $privacyTable->delete(array('action_id = ?' => $this->action_id));
    $privacyTable->createRow(array('action_id' => $this->action_id, 'privacy' => $privacy))->save();

    Engine_Api::_()->getDbTable('actions', 'wall')->resetActivityBindings($this);


  }


  public function getHref($params = array())
  {

    $slug = '';
    $object = $this->getObject();
    if ($object && method_exists($object, 'getSlug')){
      $slug = $object->getSlug($object->getTitle());
    }

    return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble(array_merge($params, array(
          'id' => $this->getIdentity(),
          'object' => $slug
        )), 'wall_view', true);
  }



  public function getPeopleTags()
  {
    $table = Engine_Api::_()->getDbTable('tags', 'wall');
    $select = $table->select()
        ->where('action_id = ?', $this->getIdentity())
        ->where('is_people = 1')
        ->order('tag_id ASC')
        ->limit(100);

    return $table->fetchAll($select);

  }

  public function getTags()
  {

    $table = Engine_Api::_()->getDbTable('tags', 'wall');
    $select = $table->select()
        ->where('action_id = ?', $this->getIdentity())
        ->where('is_people = 0')
        ->order('tag_id ASC')
        ->limit(100);

    return $table->fetchAll($select);

  }


  public function canRemoveTag(Core_Model_Item_Abstract $object)
  {
    if ($object->getType() != 'user'){
      return false;
    }
    $tags = $this->getTags();

    $has_me = false;

    foreach ($tags as $item){
      if ($item->object_type == $object->getType() && $item->object_id == $object->getIdentity()){
        $has_me = true;
      }
    }

    $people_tags = $this->getPeopleTags();

    foreach ($people_tags as $item){
      if ($item->object_type == $object->getType() && $item->object_id == $object->getIdentity()){
        $has_me = true;
      }
    }

    return $has_me;

  }




}