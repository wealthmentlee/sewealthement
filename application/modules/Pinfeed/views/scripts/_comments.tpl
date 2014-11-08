<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _comments.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */

?>

<?php
  $canComment = ( $this->action->getTypeInfo()->commentable &&
      $this->viewer()->getIdentity() &&
      Engine_Api::_()->authorization()->isAllowed($this->action->getObject(), null, 'comment') &&
      !empty($this->commentForm) );
?>


<?php if( $this->action->getTypeInfo()->commentable ): // Comments - likes ?>

  <?php

    $comments = null;
    $paginator = null;

    if ($this->comment_pagination){

      $select = $this->action->comments()->getCommentSelect('DESC');
      $select->order('comment_id DESC');

      $paginator = Zend_Paginator::factory($select);

      if (!empty($this->comment_page)){
        $paginator->setCurrentPageNumber($this->comment_page);
      }
      $paginator->setItemCountPerPage(10);

      $comments = array();
      foreach ($paginator->getCurrentItems() as $item){
        $comments[] = $item;
      }

      $comments = array_reverse($comments);


    } else {
      $comments = $this->action->getComments($this->viewAllComments);
    }
  ?>

      <?php if( $this->action->likes()->getLikeCount() > 0 && (count($this->action->likes()->getAllLikesUsers())>0) ): ?>

      <?php
        $like_users = array();

        foreach ($this->action->likes()->getAllLikesUsers() as $user){
          if (Engine_Api::_()->wall()->isOwnerTeamMember($this->action->getObject(), $user)){
            $page = Engine_Api::_()->wall()->getSubjectPage($this->action->getObject());
            $like_users[] = $page;
          } else {
            $like_users[] = $user;
          }
        }

?>

        <li class="container-comment_likes">
          <div></div>
          <div class="comments_likes">
            <?php if( $this->action->likes()->getLikeCount() <= 3 || $this->viewAllLikes ): ?>
              <?php echo $this->translate(array('%s likes this.', '%s like this.', $this->action->likes()->getLikeCount()), $this->wallFluentList($like_users) )?>

            <?php else: ?>
              <?php echo $this->htmlLink($this->action->getHref(array('action_id' => $this->action->action_id, 'show_likes' => true)),
                $this->translate(array('%s person likes this', '%s people like this', $this->action->likes()->getLikeCount()), $this->locale()->toNumber($this->action->likes()->getLikeCount()) )
              ) ?>
            <?php endif; ?>
          </div>
        </li>
      <?php endif; ?>

      <?php if ($this->comment_pagination):?>

        <?php if (isset($paginator->getPages()->next)):?>
          <li class="pagination">
            <a href="javascript:void(0);" class="comment_next" rev="item_<?php echo $paginator->getPages()->next?>"><?php echo $this->translate('WALL_COMMENT_NEXT')?></a>
            <div class="count">
              <?php echo $this->translate('WALL_COMMENT_COUNT', array($paginator->getCurrentPageNumber()*$paginator->getCurrentItemCount(), $paginator->getTotalItemCount()))?>
            </div>
          </li>
        <?php endif;?>

      <?php else :?>

        <?php if( $this->action->comments()->getCommentCount() > 0 ): ?>
          <?php if( $this->action->comments()->getCommentCount() > 5 && !$this->viewAllComments): ?>
            <li>
              <div></div>
              <div class="comments_viewall">
                <?php if( $this->action->comments()->getCommentCount() > 2): ?>

                  <?php echo $this->htmlLink($this->action->getHref(array('comment_pagination' => true)),
                      $this->translate(array('View all %s comment', 'View all %s comments', $this->action->comments()->getCommentCount()),
                      $this->locale()->toNumber($this->action->comments()->getCommentCount()))) ?>

                <?php endif; ?>
              </div>

            </li>
          <?php endif; ?>

        <?php endif; ?>

      <?php endif; ?>


      <?php if ($comments): ?>

        <?php foreach($comments  as $comment ): ?>
          <li rev="item-<?php echo $comment->comment_id ?>" class="wall-comment-item" id="comment-<?php echo $comment->comment_id ?>">
	<!-- change code -->
             <div class="comments_author_photo">


               <?php if (Engine_Api::_()->wall()->isOwnerTeamMember($this->action->getObject(), $comment->getOwner())): ?>

                <?php echo $this->htmlLink($this->action->getObject()->getHref(),
                  $this->itemPhoto($this->action->getObject(), 'thumb.icon', $this->action->getObject()->getTitle()),
                  array('class' => 'wall_liketips', 'rev' => $this->action->getObject()->getGuid())) ?>
                <?php
						$tbl_fieldValues = Engine_Api::_()->fields()->getTable('user', 'values');
						$selectPro = $tbl_fieldValues->select()
											->where("item_id =?",$this->action->getObject()->getIdentity())->where('field_id =?',24);				
						$proVal = $tbl_fieldValues->fetchRow($selectPro);
						if($proVal->value!=''){
						$optiontable = Engine_Api::_()->fields()->getTable('user', 'options');
						
						$selectLabel = $optiontable->select()->where("option_id =?",$proVal->value);
						$label = $tbl_fieldValues->fetchRow($selectLabel);
						$isprofessional = $label->label;
						if($isprofessional== 'Yes'){
					?>
						<div class="badge"><img src="./application/modules/Pinfeed/externals/images/badge.png"></div>
					<?php } }?>
               <?php else :?>

                <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(),
                  $this->itemPhoto($this->item($comment->poster_type, $comment->poster_id), 'thumb.icon', $this->action->getSubject()->getTitle()),
                  array('class' => 'wall_liketips', 'rev' => $this->item($comment->poster_type, $comment->poster_id)->getGuid())) ?>
				    <?php
						$tbl_fieldValues = Engine_Api::_()->fields()->getTable('user', 'values');
						$selectPro = $tbl_fieldValues->select()
											->where("item_id =?",$this->item($comment->poster_type, $comment->poster_id)->getIdentity())->where('field_id =?',24);				
						$proVal = $tbl_fieldValues->fetchRow($selectPro);
						if($proVal->value!=''){
						$optiontable = Engine_Api::_()->fields()->getTable('user', 'options');
						
						$selectLabel = $optiontable->select()->where("option_id =?",$proVal->value);
						$label = $tbl_fieldValues->fetchRow($selectLabel);
						$isprofessional = $label->label;
						if($isprofessional== 'Yes'){
					?>
						<div class="badge"><img src="./application/modules/Pinfeed/externals/images/badge.png"></div>
					<?php } }?>
				
               <?php endif ;?>

             </div>
	<!-- change code -->
			  
             <div class="comments_info">
               <span class='comments_author'>

                 <?php if (Engine_Api::_()->wall()->isOwnerTeamMember($this->action->getObject(), $comment->getOwner())): ?>

                    <?php echo $this->htmlLink($this->action->getObject()->getHref(), $this->action->getObject()->getTitle(), array('class' => 'wall_liketips', 'rev' => $this->action->getObject()->getGuid())); ?>

                 <?php else :?>

                    <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle(), array('class' => 'wall_liketips', 'rev' => $this->item($comment->poster_type, $comment->poster_id)->getGuid())); ?>

                 <?php endif ;?>

               </span>

                <?php if (Engine_Api::_()->getApi('settings', 'core')->getSetting('wall.content.nl2br', false)):?>
                  <?php echo $this->wallViewMore(nl2br($comment->body)) ?>
                <?php else : ?>
                  <?php echo $this->wallViewMore($comment->body) ?>
                <?php endif;?>
               

               <div class="comments_date">
                 <?php echo $this->timestamp($comment->creation_date); ?>

                  <?php if( $canComment ):
                    $isLiked = $comment->likes()->isLike($this->viewer());
                    ?>
                    -
                    <?php if( !$isLiked ): ?>
                      <a href="javascript:void(0)" class="comment-like">
                        <?php echo $this->translate('like') ?>
                      </a>
                    <?php else: ?>
                      <a href="javascript:void(0)" class="comment-unlike">
                        <?php echo $this->translate('unlike') ?>
                      </a>
                    <?php endif ?>

                  <?php endif ?>

                 <?php if ( $this->viewer()->getIdentity() &&
                   (('user' == $this->action->subject_type && $this->viewer()->getIdentity() == $this->action->subject_id) ||
                       ($this->viewer()->getIdentity() == $comment->poster_id) ||
                       $this->activity_moderate ) ): ?>
                    - <a href="javascript:void(0);" class="comment-remove"><?php echo $this->translate('Delete')?></a>
                  <?php endif; ?>

                  <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
                    -
                    <a href="<?php echo $this->url(array('controller' => 'items', 'fn' => 'like', 'm' => 'wall', 'subject' => $comment->getGuid()), 'wall_extended', true) ?>" class="comments_comment_likes smoothbox" >
                      <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                    </a>
                  <?php endif ?>

               </div>
             </div>
          </li>
        <?php endforeach; ?>

      <?php endif;?>

<?php endif; ?>
