<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<div class='comments' id="comments">
  <div class='comments_options'>
    <span><?php echo $this->translate(array('%s comment', '%s comments', $this->comments->getTotalItemCount()), $this->locale()->toNumber($this->comments->getTotalItemCount())) ?></span>
    <?php if( $this->viewer()->getIdentity() ): ?>
      <?php if( $this->subject()->likes()->isLike($this->viewer()) ): ?>
        - <a href="<?php echo $this->url(array('controller'=>'comment', 'action'=>'unlike', 'module'=>'core', 'type'=>$this->subject()->getType(), 'id'=>$this->subject()->getIdentity()), 'default', true); ?>"><?php echo $this->translate('Unlike This') ?></a>
      <?php else: ?>
				- <a href="<?php echo $this->url(array('controller'=>'comment', 'action'=>'like', 'module'=>'core', 'type'=>$this->subject()->getType(), 'id'=>$this->subject()->getIdentity()), 'default', true); ?>"><?php echo $this->translate('Like This') ?></a>
      <?php endif; ?>
      <?php if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('suggest')): ?>
        <?php
        $suggest_type = 'link_'.$this->subject()->getType();
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR ."modules" . DIRECTORY_SEPARATOR . "Mobile" . DIRECTORY_SEPARATOR .
          "modules" . DIRECTORY_SEPARATOR . "Suggest" . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "scripts";
        ?>
        <?php if (Engine_Api::_()->suggest()->isAllowed($suggest_type)):
          $paramStr = '?m=suggest&l=getSuggestItems&nli=0&params[object_type]=' . $this->subject()->getType() . '&params[object_id]=' . $this->subject()->getIdentity() .
            '&action_url='.urlencode($this->url(array('action' => 'suggest'), 'suggest_general')) .
            '&params[suggest_type]=' . $suggest_type . '&params[scriptpath]=' . $path . '&return_url=' . urlencode($_SERVER['REQUEST_URI']);

          $url = $this->url(array('controller' => 'index','action' => 'contacts','module' => 'hecore'),'default', true) . $paramStr;
          ?>
          - <a href="<?php echo $url; ?>">
              <?php echo $this->translate('Suggest') ?>
            </a>
        <?php endif; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  
  <ul>
    
    <?php if( $this->likes->getTotalItemCount() > 0 ): // LIKES ------------- ?>
      <li>
        <?php $this->likes->setItemCountPerPage($this->likes->getTotalItemCount()) ?>
          <div class="comments_likes">
            <?php echo $this->translate(array('%s likes this', '%s like this', $this->likes->getTotalItemCount()), $this->fluentList($this->subject()->likes()->getAllLikesUsers())) ?>
         </div>
    <?php endif; ?>

    <?php if( $this->comments->getTotalItemCount() > 0 ): // COMMENTS ------- ?>

      <?php // Iterate over the comments backwards (or forwards!)
      $comments = $this->comments->getIterator();
        $i = count($comments) - 1;
        $l = count($comments);
        $d = -1;
        $e = -1;
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
            <?php echo $this->mobileSubstr($comment->body) ?>
            <div class="comments_date">
              <?php echo $this->timestamp($comment->creation_date); ?>
            </div>
            <?php if( $canDelete ): ?>
              <div class="comments_comment_options">
                <a href="<?php echo $this->url(array('controller'=>'comment', 'action'=>'delete', 'module'=>'core', 'type'=>$this->subject()->getType(), 'id'=>$this->subject()->getIdentity(), 'comment_id'=>$comment->comment_id), 'default', true); ?>">
                  <?php echo $this->translate('delete') ?>
                </a>
              </div>
            <?php endif; ?>
          </div>
        </li>
      <?php endfor; ?>

    <?php endif; ?>

  </ul>

  <?php if( isset($this->form) ) echo $this->form->render() ?>
</div>