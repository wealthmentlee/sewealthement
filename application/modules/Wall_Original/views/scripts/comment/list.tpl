<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */

$itemParams = array(
 $this->subject->getType(),
 $this->subject->getIdentity()
);

$this->headTranslate(array(
  'Are you sure you want to delete this?',
));

?>

<?php if( !$this->page ): ?>

<?php
  $element_key = 'wall_comment_' . rand(1111,9999);
?>

<script type="text/javascript">

  Wall.runonce.add(function (){
    new Wall.Comment({'element_key': $$('.<?php echo $element_key?>')[0]});
  });
  
</script>


<div class='comments <?php echo $element_key?> <?php if (isset($this->params['class'])){ echo $this->params['class']; }?>' id="<?php if (isset($this->params['id'])){ echo $this->params['id']; }?>">
<?php endif; ?>
  <div class='comments_options'>
    <span><?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span>

    <?php if( isset($this->form) ): ?>
      - <a href='javascript:void(0);' class="post-comment"><?php echo $this->translate('Post Comment') ?></a>
    <?php endif; ?>

    <?php if( $this->viewer->getIdentity() && $this->canComment ): ?>

      <?php if( $this->subject->likes()->isLike($this->viewer) ): ?>
        - <a href="javascript:void(0);" class="unlike" rev='<?php echo $this->jsonInline($itemParams)?>'><?php echo $this->translate('Unlike This') ?></a>
      <?php else: ?>
        - <a href="javascript:void(0);" class="like" rev='<?php echo $this->jsonInline($itemParams)?>'><?php echo $this->translate('Like This') ?></a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <ul>

    <?php if( $this->likes->getTotalItemCount() > 0 ): // LIKES ------------- ?>

      <?php
        $like_users = array();

        foreach ($this->subject->likes()->getAllLikesUsers() as $user){
          if (Engine_Api::_()->wall()->isOwnerTeamMember($this->subject, $user)){
            $page = Engine_Api::_()->wall()->getSubjectPage($this->subject);
            $like_users[] = $page;
          } else {
            $like_users[] = $user;
          }
        }

?>
      <li>
        <?php if( $this->viewAllLikes || $this->likes->getTotalItemCount() <= 3 ): ?>
          <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
          <div> </div>
      <?php echo $this->translate(array('%s likes this', '%s like this', $this->likes->getTotalItemCount()), $this->wallFluentList($like_users)) ?>
      <div class="comments_likes">
          </div>
        <?php else: ?>
          <div> </div>
          <div class="comments_likes">
            <?php echo $this->htmlLink('javascript:void(0);', 
                          $this->translate(array('%s person likes this', '%s people like this', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount())),
                          array('class' => 'show-likes', 'rev' => $this->jsonInline($itemParams))
                      ); ?>
          </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if( $this->comments->getTotalItemCount() > 0 ): // COMMENTS ------- ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() > 1 ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'), array(
               'class' => 'load-comments',
               'rev' => $this->jsonInline(array_merge($itemParams, array($this->page - 1)))
            )) ?>
          </div>
        </li>
      <?php endif; ?>

      <?php if( !$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array(
               'class' => 'load-comments',
               'rev' => $this->jsonInline(array_merge($itemParams, array($this->comments->getCurrentPageNumber())))
            )) ?>
          </div>
        </li>
      <?php endif; ?>

      <?php // Iterate over the comments backwards (or forwards!)
      $comments = $this->comments->getIterator();
      if( $this->page ):
        $i = 0;
        $l = count($comments) - 1;
        $d = 1;
        $e = $l + 1;
      else:
        $i = count($comments) - 1;
        $l = count($comments);
        $d = -1;
        $e = -1;
      endif;
      for( ; $i != $e; $i += $d ):
        $comment = $comments[$i];
        $poster = $this->item($comment->poster_type, $comment->poster_id);
        $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer) );

        $commentParams = array(
          $this->subject->getType(),
          $this->subject->getIdentity(),
          $comment->getIdentity()
        );


        ?>
        <li class="comment-<?php echo $comment->comment_id ?>">
          <div class="comments_author_photo">
            <?php if (Engine_Api::_()->wall()->isOwnerTeamMember($this->subject, $poster)):?>
              <?php
                $page = Engine_Api::_()->wall()->getSubjectPage($this->subject);
              ?>
              <?php echo $this->htmlLink($page->getHref(),
                $this->itemPhoto($page, 'thumb.icon', $page->getTitle(), array('class' => 'wall_liketips', 'rev' => $page->getGuid()))
              ) ?>
            <?php else: ?>
              <?php echo $this->htmlLink($poster->getHref(),
                $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle(), array('class' => 'wall_liketips', 'rev' => $poster->getGuid()))
              ) ?>
            <?php endif;?>
          </div>
          <div class="comments_info">
            <?php if (Engine_Api::_()->wall()->isOwnerTeamMember($this->subject, $poster)):?>
              <?php
                $page = Engine_Api::_()->wall()->getSubjectPage($this->subject);
              ?>
              <span class='comments_author'><?php echo $this->htmlLink($page->getHref(), $page->getTitle(), array('class' => 'wall_liketips', 'rev' => $page->getGuid())); ?></span>
            <?php else: ?>
              <span class='comments_author'><?php echo $this->htmlLink($poster->getHref(), $poster->getTitle(), array('class' => 'wall_liketips', 'rev' => $poster->getGuid())); ?></span>
            <?php endif;?>

            <?php echo $this->viewMore($comment->body) ?>
            <div class="comments_date">
              <?php echo $this->timestamp($comment->creation_date); ?>
              <?php if( $canDelete ): ?>
                -
                <a href="javascript:void(0);" class="delete-comment" rev='<?php echo $this->jsonInline($commentParams)?>'>
                  <?php echo $this->translate('delete') ?>
                </a>
              <?php endif; ?>
              <?php if( $this->canComment ):
                $isLiked = $comment->likes()->isLike($this->viewer);
                ?>
                -
                <?php if( !$isLiked ): ?>
                  <a href="javascript:void(0)" class="comment-like" rev='<?php echo $this->jsonInline($commentParams)?>'>
                    <?php echo $this->translate('like') ?>
                  </a>
                <?php else: ?>
                  <a href="javascript:void(0)" class="comment-unlike" rev='<?php echo $this->jsonInline($commentParams)?>'>
                    <?php echo $this->translate('unlike') ?>
                  </a>
                <?php endif ?>
              <?php endif ?>
              <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
                -
                <a href="<?php echo $this->url(array('controller' => 'items', 'fn' => 'like', 'm' => 'wall', 'subject' => $comment->getGuid()), 'wall_extended', true) ?>" class="smoothbox"  class="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes">
                  <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                </a>
              <?php endif ?>
            </div>
            <?php /*
            <div class="comments_date">
              <?php echo $this->timestamp($comment->creation_date); ?>
              <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
                -
                <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
                  <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
                </a>
              <?php endif ?>
            </div>
            <div class="comments_comment_options">
              <?php if( $canDelete && $this->canComment ): ?>
                -
              <?php endif ?>
            </div>
             *
             */ ?>
          </div>
        </li>
      <?php endfor; ?>

      <?php if( $this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
        <li>
          <div> </div>
          <div class="comments_viewall">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
              'class' => 'load-comments',
              'rev' => $this->jsonInline(array_merge($itemParams, array($this->page + 1)))
            )) ?>
          </div>
        </li>
      <?php endif; ?>

    <?php endif; ?>

  </ul>

  <?php if( isset($this->form) ) echo $this->form->setAttribs(array('class' => 'comment-form', 'style' => 'display:none;'))->render() ?>
<?php if( !$this->page ): ?>
</div>
    <?php endif; ?>