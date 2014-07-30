<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Votes.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hequestion_Model_DbTable_Votes extends Engine_Db_Table
{

  public function getVoteSelect($question_id, User_Model_User $object, $option_id = null)
  {
    $select = $this->select()
        ->where('question_id = ?', $question_id)
        ->where('user_id = ?', $object->getIdentity());

    if ($option_id){
      $select->where('option_id = ?', $option_id);
    }

    return $select;

  }


}