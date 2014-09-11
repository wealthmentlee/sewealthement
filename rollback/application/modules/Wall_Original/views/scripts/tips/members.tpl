<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: members.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php
  if ($this->members instanceof Zend_Paginator){
    $this->members->setItemCountPerPage(5);
  }
?>

<ul class="items users">
  <?php foreach ($this->members as $member):?>

  <?php
  $user = null;
  if ($member instanceof User_Model_User){
    $user = $member;
  } else if ($member instanceof Engine_Db_Table_Row && isset($member->user_id)){
    $user = Engine_Api::_()->getItem('user', $member->user_id);
  }

  if (!$user){
    continue ;
  }

?>

    <li>
      <a href="<?php echo $user->getHref()?>" title="<?php echo $user->getTitle()?>">
        <?php echo $this->itemPhoto($user, 'thumb.icon')?>
      </a>
    </li>
  <?php endforeach;?>
</ul>