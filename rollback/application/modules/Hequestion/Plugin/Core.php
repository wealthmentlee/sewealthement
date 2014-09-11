<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */



class Hequestion_Plugin_Core
{
  public function onStatistics($event)
  {
    $table = Engine_Api::_()->getDbTable('questions', 'hequestion');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'questions');

    $table = Engine_Api::_()->getDbTable('votes', 'hequestion');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count')->group('user_id');
    $event->addResponse($select->query()->fetchColumn(0), 'question votes');
  }

  public function onUserDeleteBefore($event)
  {
    $payload = $event->getPayload();
    if ($payload instanceof User_Model_User){
      $questionTable = Engine_Api::_()->getDbtable('questions', 'hequestion');
      $questionSelect = $questionTable->select()->where('user_id = ?', $payload->getIdentity());
      foreach ($questionTable->fetchAll($questionSelect) as $question){
        $question->delete();
      }
    }
  }
}