<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version		$Id: list.tpl 8375 2011-02-02 02:09:25Z john $
 * @author     John
 */
?>
<div class='comments_options'>
	<span id="comments_count_<?php echo $this->subject->getGuid(); ?>"><?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span>
	<?php if( isset($this->form) ): ?>
		- <a href='javascript:void(0);' id="post_comment_<?php echo $this->subject->getGuid(); ?>" onclick="$('comment-form').style.display = '';$('comment-form').body.focus();"><?php echo $this->translate('Post Comment') ?></a>
	<?php endif; ?>
<?php if( $this->viewer()->getIdentity() ): ?>
		<?php if( $this->subject()->likes()->isLike($this->viewer()) ): ?>
			- <a href="javascript:void(0);" id="comments_unlike_<?php echo $this->subject->getGuid(); ?>" onclick="en4.core.comments.unlike('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>')"><?php echo $this->translate('Unlike This') ?></a>
		<?php else: ?>
			- <a href="javascript:void(0);" id="comments_like_<?php echo $this->subject->getGuid(); ?>" onclick="en4.core.comments.like('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>')"><?php echo $this->translate('Like This') ?></a>
		<?php endif; ?>
	<?php endif; ?>
</div>
<ul>
<?php if( $this->likes->getTotalItemCount() > 0 ): // LIKES ------------- ?>
  <li>
    <?php if( $this->viewAllLikes || $this->likes->getTotalItemCount() <= 3 ): ?>
      <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
      <div> </div>
      <div id="comments_likes_list_<?php echo $this->subject->getGuid(); ?>" class="comments_likes">
        <?php echo $this->translate(array('%s likes this', '%s like this', $this->likes->getTotalItemCount()), $this->fluentList($this->subject()->likes()->getAllLikesUsers())) ?>
      </div>
    <?php else: ?>
      <div> </div>
      <div id="comments_likes_list_<?php echo $this->subject->getGuid(); ?>" class="comments_likes">
        <?php echo $this->htmlLink('javascript:void(0);',
              $this->translate(array('%s person likes this', '%s people like this', $this->likes->getTotalItemCount()), $this->locale()->toNumber($this->likes->getTotalItemCount())),
              array(
                //'onclick' => 'en4.core.comments.showLikes("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'");'
                'id' => 'show_likes_'.$this->subject->getGuid()
              ));
        ?>
      </div>
    <?php endif; ?>
<?php endif; ?>
<?php if( $this->comments->getTotalItemCount() > 0 ): // COMMENTS ------- ?>
  <?php if( $this->page && $this->comments->getCurrentPageNumber() > 1 ): ?>
    <li>
      <div> </div>
      <div class="comments_viewall">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View previous comments'), array(
          //'onclick' => 'en4.core.comments.loadComments("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'", "'.($this->page - 1).'")'
          'id' => 'comments_view_all_'.$this->subject()->getGuid().'_'.($this->page - 1),
          'class' => 'comments_view_all_'.$this->subject()->getGuid()
        )) ?>
      </div>
    </li>
  <?php endif; ?>
  <?php if( !$this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
    <li>
      <div> </div>
      <div class="comments_viewall">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View more comments'), array(
          //'onclick' => 'en4.core.comments.loadComments("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'", "'.($this->comments->getCurrentPageNumber()).'")'
          'id' => 'comments_view_all_'.$this->subject()->getGuid().'_'.$this->comments->getCurrentPageNumber(),
          'class' => 'comments_view_all_'.$this->subject()->getGuid()
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
    $canDelete = ( $this->canDelete || $poster->isSelf($this->viewer()) );
    ?>
    <li id="comment-<?php echo $comment->comment_id ?>">
      <div class="comments_author_photo">
        <?php echo $this->htmlLink($poster->getHref(),
          $this->itemPhoto($poster, 'thumb.icon', $poster->getTitle())
        ) ?>
      </div>
      <div class="comments_info">
        <span class='comments_author'><?php echo $this->htmlLink($poster->getHref(), $poster->getTitle()); ?></span>
        <?php echo $this->viewMore($comment->body) ?>
        <div class="comments_date">
          <?php echo $this->timestamp($comment->creation_date); ?>
          <?php if( $canDelete ): ?>
            -
            <a href="javascript:void(0);" onclick="en4.core.comments.deleteComment('<?php echo $this->subject()->getType()?>', '<?php echo $this->subject()->getIdentity() ?>', '<?php echo $comment->comment_id ?>')">
              <?php echo $this->translate('delete') ?>
            </a>
          <?php endif; ?>
          <?php if( $this->canComment ):
            $isLiked = $comment->likes()->isLike($this->viewer());
            ?>
            -
            <?php if( !$isLiked ): ?>
              <a href="javascript:void(0)" onclick="en4.core.comments.like(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)">
                <?php echo $this->translate('like') ?>
              </a>
            <?php else: ?>
              <a href="javascript:void(0)" onclick="en4.core.comments.unlike(<?php echo sprintf("'%s', %d, %d", $this->subject()->getType(), $this->subject()->getIdentity(), $comment->getIdentity()) ?>)">
                <?php echo $this->translate('unlike') ?>
              </a>
            <?php endif ?>
          <?php endif ?>
          <?php if( $comment->likes()->getLikeCount() > 0 ): ?>
            -
            <a href="javascript:void(0);" id="comments_comment_likes_<?php echo $comment->comment_id ?>" class="comments_comment_likes" title="<?php echo $this->translate('Loading...') ?>">
              <?php echo $this->translate(array('%s likes this', '%s like this', $comment->likes()->getLikeCount()), $this->locale()->toNumber($comment->likes()->getLikeCount())) ?>
            </a>
          <?php endif ?>
        </div>
      </div>
    </li>
  <?php endfor; ?>
  <?php if( $this->page && $this->comments->getCurrentPageNumber() < $this->comments->count() ): ?>
    <li>
      <div> </div>
      <div class="comments_viewall">
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View later comments'), array(
          'onclick' => 'en4.core.comments.loadComments("'.$this->subject()->getType().'", "'.$this->subject()->getIdentity().'", "'.($this->page + 1).'")'
        )) ?>
      </div>
    </li>
  <?php endif; ?>
<?php endif; ?>
</ul>
<?php if( isset($this->form) ) echo $this->form->setAttribs(array('id' => 'comment-form_'.$this->subject->getGuid(), 'style' => 'display:none;'))->render() ?>