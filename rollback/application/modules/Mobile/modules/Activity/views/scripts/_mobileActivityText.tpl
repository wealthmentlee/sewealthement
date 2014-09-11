<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _mobileActivityText.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<?php if( empty($this->actions) ) { echo $this->translate("The action you are looking for does not exist."); return; } else { $actions = $this->actions; } ?>

<?php
  foreach( $actions as $action ):
    try {
      if( !$action->getTypeInfo()->enabled ) continue;
      if( !$action->getSubject() || !$action->getSubject()->getIdentity() ) continue;
      if( !$action->getObject() || !$action->getObject()->getIdentity() ) continue;
      ob_start();
    ?>
    <?php if( !$this->noList ): ?><li><?php endif; ?>

    <?php if( isset($this->commentForm) ): ?>
      <?php $this->commentForm->setActionIdentity($action->action_id) ?>
    <?php endif; ?>


    <div class='item_photo'>
      <?php echo $this->htmlLink(array('id' => $action->getSubject()->user_id, 'route' => 'user_profile'),
      $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle())
      ) ?>
    </div>


    <div class='item_body'>
      <?php // Main Content ?>
      <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">

        <?php if ($action->type == 'like_item_private'): ?>

            <?php 
              if ($this->subject() && $this->subject()->getIdentity() == $this->viewer()->getIdentity()) {
                $like_item_private = $this->translate('like_I like %s.', $this->htmlLink($action->getObject()->getHref(), $action->getObject()->getTitle()));
              } else {
                $item_object_link = $this->htmlLink($action->getObject()->getHref(), $action->getObject()->getTitle());
                $item_subject_link = $this->htmlLink($action->getSubject()->getHref(), $action->getSubject()->getTitle());
                $like_item_private = $this->translate('like_%s likes %s.', array($item_subject_link, $item_object_link));
              }
            ?>
            <div class="like-action-private">
              <span class="text"><?= $like_item_private; ?></span>
            </div>

        <?php elseif ($action->type == 'like_item') : ?>

          <?php echo $this->stripHtmlTag($action->getContent(), 'script'); ?>

        <?php else : ?>

          <?php
            Engine_Api::_()->activity()->getHelper('body')->setAction($action);
            echo $action->getContent();
          ?>

        <?php endif; ?>

      </span>

      <?php // Attachments ?>
      <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
        <div class='feed_item_attachments mobile_feed_item_attachments'>
          <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
            <?php if( count($action->getAttachments()) == 1 &&
                    null != ( $richContent = current($action->getAttachments())->item->getRichContent()) ): ?>
              <?php echo $richContent; ?>
            <?php else: ?>
              <?php foreach( $action->getAttachments() as $attachment ): ?>
                <span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
                <?php if( $attachment->meta->mode == 0 ): // Silence ?>
                <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
                  <div>
                    <div class="feed_item_attachment_photo">
                    <?php if( $attachment->item->getPhotoUrl() ): ?>
                      <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle())) ?>
                    <?php endif; ?>
                    </div>
                    <div class="feed_item_attachment_body">
                      <?php
                        if ($attachment->item->getType() == "core_link")
                        {
                          $attribs = Array('target'=>'_blank');
                        }
                        else
                        {
                          $attribs = Array();
                        }
                        echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                      ?>
                    </div>
                    <div class='feed_item_link_desc'>
                        <?php echo ($this->full_text) ? $attachment->item->getDescription() : $this->mobileSubstr($attachment->item->getDescription())?>
                    </div>
                  </div>
                <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
                  <div class="feed_attachment_photo">
                    <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), array('class' => 'feed_item_thumb')) ?>
                  </div>
                <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
                  <?php echo ($this->full_text) ? $attachment->item->getDescription() : $this->mobileSubstr($attachment->item->getDescription()); ?>
                <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
                <?php endif; ?>
                </span>
              <?php endforeach; ?>
              <?php endif; ?>
          <?php endif; ?>
        </div><div class="clr"></div>
      <?php endif; ?>

      <?php // Icon, time since, action links ?>
      <?php
        $icon_type = '';
        list($attachment) = $action->getAttachments();
        if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item ):
          $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
        endif;
      ?>
      <div class='item_date'>
        <?php echo $this->timestamp($action->getTimeValue()) ?>
        <?php if( $action->getTypeInfo()->commentable && $this->viewer()->getIdentity() && Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') ): ?>
          <?php if( $action->likes()->isLike($this->viewer()) ): ?>
            - <?php echo $this->htmlLink($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'unlike'), 'default', true) . '?action_id=' . $action->action_id . '&return_url=' . urlencode($_SERVER['REQUEST_URI']), $this->translate('Unlike')) ?>
          <?php else: ?>
            - <?php echo $this->htmlLink($this->url(array('module'=>'activity', 'controller'=>'index', 'action'=>'like'), 'default', true) . '?action_id=' . $action->action_id . '&return_url=' . urlencode($_SERVER['REQUEST_URI']), $this->translate('Like')) ?>
          <?php endif; ?>

          <?php if( !isset($this->commentForm) ): ?>
          - <?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action'=>'view','action_id'=>$action->getIdentity()), $this->translate('Comment')) ?>
          <?php endif; ?>

          <?php if(  $this->activity_moderate || $this->viewer()->getIdentity() && $this->allow_delete &&
                   (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                    ('user' == $action->object_type  && $this->viewer()->getIdentity() == $action->object_id) ) ): ?>
          - <?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action' => 'delete', 'action_id'=>$action->action_id, 'return_url' => urlencode($_SERVER['REQUEST_URI'])), $this->translate('Delete')) ?>
          <?php endif; ?>

        <?php endif; ?>
      </div>

      <?php if( $action->getTypeInfo()->commentable ): // Comments - likes ?>
        <div class='comments mobile-comments'>
          <ul>
            <?php if( $action->likes()->getLikeCount() > 0 && (count($action->likes()->getAllLikesUsers())>0) ): ?>
              <li>
                <div></div>
                <div class="comments_likes">
                  <?php if( $action->likes()->getLikeCount() <= 3 || $this->viewAllLikes ): ?>
                    <?php echo $this->translate(array('%s likes this.', '%s like this.', $action->likes()->getLikeCount()), $this->fluentList($action->likes()->getAllLikesUsers()) )?>

                  <?php else: ?>
                    <?php echo $this->htmlLink(array('route' => 'user_profile', 'id' => $action->subject_id, 'action_id' => $action->action_id, 'show_likes' => true),
                                              $this->translate(array('%s person likes this', '%s people like this', $action->likes()->getLikeCount()), $this->locale()->toNumber($action->likes()->getLikeCount()) )
                    ) ?>
                  <?php endif; ?>
                </div>
              </li>
            <?php endif; ?>
            <?php if( $action->comments()->getCommentCount() > 0 ): ?>
              <?php if( $action->comments()->getCommentCount() > 5 && !$this->viewAllComments): ?>
                <li>
                  <div></div>
                  <div class="comments_viewall">
                    <?php if( $action->comments()->getCommentCount() > 2): ?>
                      <?php echo $this->htmlLink(array('route'=>'default','module'=>'activity','controller'=>'index','action'=>'view','action_id'=>$action->getIdentity()),
                                                 $this->translate(array('View all %s comment', 'View all %s comments', $action->comments()->getCommentCount()),
                                                                  $this->locale()->toNumber($action->comments()->getCommentCount()))) ?>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endif; ?>
              <?php foreach( $action->getComments($this->viewAllComments) as $comment ): ?>
                <li id="comment-<?php echo $comment->comment_id ?>">
                   <div class="comments_info">
                     <span class='comments_author'>
                       <?php echo $this->htmlLink($this->item($comment->poster_type, $comment->poster_id)->getHref(), $this->item($comment->poster_type, $comment->poster_id)->getTitle()); ?>
                     </span>
                     <?php echo ($this->full_text) ? $comment->body : $this->mobileSubstr($comment->body) ?>
                     <div class="comments_date">
                       <?php echo $this->timestamp($comment->creation_date); ?>
                       <?php if ( $this->viewer()->getIdentity() &&
                                 (('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
                                  ($this->viewer()->getIdentity() == $comment->poster_id) ) ): ?>
                       - <?php echo $this->htmlLink(array(
                            'route'=>'default',
                            'module'    => 'activity',
                            'controller'=> 'index',
                            'action'    => 'delete',
                            'action_id' => $action->action_id,
                            'comment_id'=> $comment->comment_id,
                            'return_url' => urlencode($_SERVER['REQUEST_URI'])
                            ), $this->translate('Delete')) ?>
                       <?php endif; ?>
                     </div>
                   </div>
                </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
          <?php if( isset($this->commentForm) ): $this->commentForm->removeAttrib('style');  echo $this->commentForm->render(); endif;?>
        </div>
      <?php endif; ?>

    </div>
  <?php if( !$this->noList ): ?></li><?php endif; ?>
<?php
      ob_end_flush();
    } catch (Exception $e) {
      ob_end_clean();
      if( APPLICATION_ENV === 'development' ) {
        echo $e->__toString();
      }
    };
  endforeach;
?>