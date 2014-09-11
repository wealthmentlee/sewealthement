<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Smiles.php 18.06.12 10:52 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Wall_Model_DbTable_Smiles extends Engine_Db_Table
{

  public function getPaginator()
  {

    $select = $this->select()
        ->where('enabled = 1')
        ->order('smile_id ASC');

    $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(300);


    return $paginator;

  }



}
