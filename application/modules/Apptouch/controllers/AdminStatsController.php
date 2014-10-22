<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 15.02.12
 * Time: 16:39
 * To change this template use File | Settings | File Templates.
 */
class Apptouch_AdminStatsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {

  }

  public function globalStatsAction()
  {
    //    // Get types
    //    $statsTable = Engine_Api::_()->getDbtable('statistics', 'core');
    //    $select = new Zend_Db_Select($statsTable->getAdapter());
    //    $select
    //      ->from($statsTable->info('name'), 'type')
    //      ->distinct(true)
    //      ;
    //
    //    $data = $select->query()->fetchAll();
    //    $types = array();
    //    foreach( $data as $datum ) {
    //      $type = $datum['type'];
    //      $fancyType = '_CORE_ADMIN_STATS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_'));
    //      $types[$type] = $fancyType;
    //    }
    //
    //    $this->view->filterForm = $filterForm = new Core_Form_Admin_Statistics_Filter();
    //    $filterForm->type->setMultiOptions($types);
  }
}
