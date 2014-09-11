<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Popular.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Plugin_Type_Popular extends Wall_Plugin_Type_Abstract
{


  public $customStream = true;

  public function getCustomStream($viewer = null, $params = array())
  {
    if (empty($viewer)){
      return ;
    }
    $params = $this->_getInfo($params);


    $tableTypes = Engine_Api::_()->getDbTable('actionTypes', 'activity');
    $select = $tableTypes->select()
        ->where('enabled = 1')
        ->where('displayable & 4')
        ->where('module IN (?)', Engine_Api::_()->getDbTable('modules', 'core')->getEnabledModuleNames());

    $types = array();
    foreach ($tableTypes->fetchAll($select) as $item){
      $types[] = $item->type;
    }

    if (empty($types)){
      return null;
    }

    $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('getActivity', array(
      'for' => $viewer,
    ));
    $responses = (array) $event->getResponses();

    if( empty($responses) ) {
      return null;
    }
    $tableStream = Engine_Api::_()->getDbTable('stream', 'activity');

    $where = '0';
    foreach ($responses as $response){

      $where .= ' OR (target_type = "'.$response['type'].'" AND ';

      if( empty($response['data']) ) {
        $where .= 'target_id = 0';
      } else if( is_scalar($response['data']) || count($response['data']) === 1 ) {
        if( is_array($response['data']) ) {
          list($response['data']) = $response['data'];
        }
        $where .= 'target_id = ' . $response['data'];
      } else if( is_array($response['data']) ) {
        $where .= 'target_id IN (' . implode(",", (array) $response['data']) . ')';
      } else {
        continue;
      }

      $where .= ')';

    }

    $limit = (empty($params['limit'])) ? 10 : $params['limit'];
    $hideIds = (empty($params['hideIds'])) ? null : $params['hideIds'];
    $page = (empty($params['page'])) ? 1 : $params['page'];

    $actionTable = Engine_Api::_()->getDbTable('actions', 'wall');

    $select = $actionTable->select()
        ->setIntegrityCheck(false)
        ->from(array('s' => $tableStream->info('name')), array())
        ->join(array('a' => $actionTable->info('name')), 'a.action_id = s.action_id', new Zend_Db_Expr('a.*'))
        ->where(new Zend_Db_Expr($where));


    if (null !== $hideIds){
      $select->where('s.action_id NOT IN(?)', $hideIds);
    }

    $select
      ->where('s.type IN (?)', $types)
      ->group('s.action_id')
      ->group('a.action_id')
      ->order('a.like_count DESC')
      ->order('a.comment_count DESC')
      ->order('a.action_id DESC')
    ;

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage($limit);
    $paginator->setCurrentPageNumber(5);


    if (!empty($paginator->getPages()->next)){
      $this->feed_config['next_page'] = $paginator->getPages()->next;
    }


    return $paginator->getCurrentItems();

  }

  protected function _getInfo(array $params)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $args = array(
      'limit' => $settings->getSetting('activity.length', 20),
      'showTypes' => null,
      'hideTypes' => null,
      'hideIds' => null,
      'page' => 1
    );

    $newParams = array();
    foreach( $args as $arg => $default ) {
      if( !empty($params[$arg]) ) {
        $newParams[$arg] = $params[$arg];
      } else {
        $newParams[$arg] = $default;
      }
    }

    return $newParams;
  }

}