<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Followers.php 17.08.12 06:04 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Hequestion_Model_DbTable_Followers extends Engine_Db_Table
{


  public function getFollow(User_Model_User $object, Hequestion_Model_Question $question)
  {
    $select = $this->select()
        ->where('question_id = ?', $question->getIdentity())
        ->where('user_id = ?', $object->getIdentity());

    return $this->fetchRow($select);

  }

  public function follow(User_Model_User $object, Hequestion_Model_Question $question)
  {
    $follow = $this->getFollow($object, $question);

    if (!$follow){
      $follow = $this->createRow();
      $follow->user_id = $object->getIdentity();
      $follow->question_id = $question->getIdentity();
      $follow->save();

      $question->follower_count++;
      $question->save();

    }

  }


  public function unfollow(User_Model_User $object, Hequestion_Model_Question $question)
  {
    $follow = $this->getFollow($object, $question);
    if ($follow){
      $follow->delete();
      $question->follower_count--;
      $question->save();
    }
  }


}