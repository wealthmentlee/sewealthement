<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */


  $subject = $this->subject;
  $viewer = $this->viewer();

  if (!$subject){
	return ;
  }
  
  $type = $subject->getType();
  
  if (!in_array($type, Engine_Api::_()->wall()->getLikeTipTypes())){
    return ;
  }

?>
<div class="content data">
  <div class="item_photo">
    <?php
      if ($subject instanceof User_Model_User){
        echo $this->htmlLink($subject->getHref(), $this->itemPhoto($subject, 'thumb.profile'));
      } else {
        echo $this->htmlLink($subject->getHref(), $this->itemPhoto($subject, 'thumb.normal'));
      }
?>
  </div>
  <div class="item_body">
    <div class="item_title">
      <?php echo $this->htmlLink($subject->getHref(), $this->truncate($subject->getTitle(), 20))?>
    </div>
    <div class="item_description">

      <?php //echo $this->truncate($subject->getDescription(),255)?>


      <?php if ($type == 'user'):?>

        <?php if ($subject->likes()->getLikeCount()):?>
          <div class="likes members_description">
            <?php echo $this->translate(array('%s like', '%s likes', $subject->likes()->getLikeCount()), $subject->likes()->getLikeCount())?>
          </div>
        <?php endif;?>


		<?php 
			$mutual_friends = null;
			if (!$subject->isSelf($viewer)){
				$mutual_friends = Engine_Api::_()->wall()->getMutualFriendsPaginator($subject, $viewer);
			}
		?>
		
        <?php if ($mutual_friends && $mutual_friends->getTotalItemCount()):?>

           <div class="mutual-friends members_description">
             <?php echo $this->translate(array('%s mutual friend', '%s mutual friends', $mutual_friends->getTotalItemCount()), $mutual_friends->getTotalItemCount());?>
           </div>
           <?php echo $this->partial('tips/members.tpl', 'wall', array('members' => $mutual_friends))?>

         <?php else :?>

           <?php if ($subject->membership()->getMemberCount()):?>

           <?php
              $paginator = Zend_Paginator::factory($subject->membership()->getMembersObjectSelect());
              $count = $paginator->getTotalItemCount(); // $subject->membership()->getMemberCount()
           ?>

             <div class="friends members_description">
               <?php echo $this->translate(array('%s friend', '%s friends', $count), $count)?>
             </div>
             <?php echo $this->partial('tips/members.tpl', 'wall', array('members' => $paginator))?>
           <?php endif;?>

         <?php endif;?>
        

      <?php elseif ($type == 'page'):?>


        <?php if ($subject->likes()->getLikeCount()):?>


          <?php if ($subject->likes()->getLikeCount()):?>
            <div class="likes members_description">
              <?php echo $this->translate(array('%s like', '%s likes', $subject->likes()->getLikeCount()), $subject->likes()->getLikeCount())?>
            </div>
          <?php endif;?>

          <?php

            $paginator = $subject->likes()->getLikePaginator();
            $paginator->setItemCountPerPage(10);

            $members_ids = array();
            foreach ($paginator as $data) {
              if ($data['poster_type'] != 'user') {
                continue;
              }
              $members_ids[] = array('type' => $data['poster_type'], 'id' => $data['poster_id']);

            }
            $members = Engine_Api::_()->wall()->getItems($members_ids);

          ?>

          <?php echo $this->partial('tips/members.tpl', 'wall', array('members' => $members))?>


        <?php endif;?>

          

      <?php elseif ($type == 'group'):?>

        <?php if ($subject->membership()->getMemberCount()):?>
          <div class="members members_description">
            <?php echo $this->translate(array('%s member', '%s members', $subject->membership()->getMemberCount()), $subject->membership()->getMemberCount())?>
          </div>
          <?php echo $this->partial('tips/members.tpl', 'wall', array('members' => Zend_Paginator::factory($subject->membership()->getMembersObjectSelect())))?>
        <?php endif;?>


      <?php elseif ($type == 'event'):?>

      <?php

        $select = $subject->membership()->getMembersSelect();
        $select->where('rsvp = ?', 2);
        $paginator = Zend_Paginator::factory($select);

      ?>

        <?php if ($paginator->getTotalItemCount()):?>
          <div class="members members_description">
            <?php echo $this->translate(array('%s member', '%s members', $paginator->getTotalItemCount()), $paginator->getTotalItemCount())?>
          </div>
          <?php echo $this->partial('tips/members.tpl', 'wall', array('members' => $paginator))?>
        <?php endif;?>


      <?php endif;?>


    </div>
  </div>

</div>

<div class="item_options">


  <ul>

    <?php
      $object = Engine_Api::_()->getApi('tips', 'wall');
      if (method_exists($object, $type)){

        $result = $object->$type($subject);

        foreach ($result as $item){
          if (!$item){
            continue ;
          }
          $url = $this->url( (empty($item['params'])) ? array() : $item['params'], (empty($item['route'])) ? 'default': $item['route'], true );
          $label = (empty($item['label'])) ? '' : $this->translate($item['label']);
          $style = (empty($item['icon'])) ? '' : 'background-image: url('.$item['icon'].')';
          $class = (empty($item['class'])) ? '' : $item['class'];

          echo '<li><a href="'.$url.'" style="'.$style.'" class="buttonlink '.$class.'">'.$label.'</a></li>';
        }
      }
    ?>


   </ul>


</div>