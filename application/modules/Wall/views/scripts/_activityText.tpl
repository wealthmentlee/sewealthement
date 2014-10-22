<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _activityText.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
$wealthApi = Engine_Api::_()->wealthment();
?>



<?php

if (empty($this->actions)) {
  echo $this->translate("The action you are looking for does not exist.");
  return;
} else {
  $actions = $this->actions;
  $checkins = $this->wallActivityCheckins($actions);
  $hasgtags = $this->wallActivityHashtags($actions);


  $pageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
  $eventEnabled = Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('event');
}

?>
<script type="text/javascript">

  $$('.link_hashtag_div ').addEvent('mouseover', function(event) {
    event = new Event(event).stop();
    box = 1;
  });
  $$('.box_for_hashtag ').addEvent('mouseover', function(event) {
    event = new Event(event).stop();
    box = 1;
  });
  $$('.box_for_hashtag ').addEvent('mouseout', function(event) {
    event = new Event(event).stop();
    box = 0;
  });
  $$('.link_hashtag_div ').addEvent('mouseout', function(event) {
    event = new Event(event).stop();
    box = 0;
    setTimeout(function (){
      if(box != 1){
        $$('.box_for_hashtag ').addClass('display_none');
      }
    }, 500);
  });

  $$('.box_for_hashtag ').addEvent('mouseout', function(event) {
    event = new Event(event).stop();

    setTimeout(function (){
      if(box != 1){
        $$('.box_for_hashtag ').addClass('display_none');
      }
    }, 100);
  });

</script>
<style>
    .bar_1{
       background-color: #03b1da;
       height: 20px;
       color: #fff;
	   padding: 5px;
	   width: 100%;
    }
	.bar_3 {
		background-color: #f9991c;
		height: 20px;
		color: #fff;
		padding: 5px;
		width: 100%;
	}
	.bar_2 {
		background-color: #8cc73d;
		height: 20px;
		color: #fff;
		padding: 5px;
		width: 100%;
	}
	.bar_4 {
		background-color: #4f4f51;
		height: 20px;
		color: #fff;
		padding: 5px;
		width: 100%;
	}
	.bar_0 {
		background-color: #adafb2;
		height: 20px;
		color: #fff;
		padding: 5px;
		width: 100%;
	}
    .wall-action-item
    {
        border-top-width: 0px !important;
        padding-top: 0px !important;
    }
</style>
<?php $this->headScript()
  ->appendFile($this->wallBaseUrl() . 'externals/flowplayer/flashembed-1.0.1.pack.js');?>

<?php
$id_hash = 0;
foreach ($actions as $action): // (goes to the end of the file)
  try { // prevents a bad feed item from destroying the entire page
    // Moved to controller, but the items are kept in memory, so it shouldn't hurt to double-check
    if (!$action->getTypeInfo()->enabled) continue;
    if (!$action->getSubject() || !$action->getSubject()->getIdentity()) continue;
    if (!$action->getObject() || !$action->getObject()->getIdentity()) continue;

    ob_start();
    ?>
    <?php if (!$this->noList): ?>
      <li rev="item-<?php echo $action->action_id ?>" class="wall-action-item">

    <?php endif; ?>
          <!-- @modified by Gitesh Dang get Label of category -->
          <div class="bar_<?php echo $action->cat ?>">
              <?php echo $wealthApi->getCatLabel($action->cat); ?>
          </div>
          
    <?php if (isset($action->grouped_subjects) && count($action->grouped_subjects) > 1): ?>

      <div class='feed_item_body'>

        <?php // Icon, time since, action links ?>
        <?php
        $icon_type = 'activity_icon_' . $action->type;
        list($attachment) = $action->getAttachments();
        if (is_object($attachment) && $action->attachment_count > 0 && $attachment->item):
          $icon_type .= ' item_icon_' . $attachment->item->getType() . ' ';
        endif;
        ?>

        <?php // Main Content ?>
        <span
          class="wall_grouped_feed_item <?php echo $icon_type ?> <?php echo(empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated') ?>">


          <?php

          $profile_url = $this->htmlLink($action->getSubject()->getHref(), $action->getSubject()->getTitle(), array('class' => 'wall_liketips', 'rev' => $action->getSubject()->getGuid()));
          $first_item = (isset($action->grouped_subjects[0])) ? $action->grouped_subjects[0] : '';
          $other_num = count($action->grouped_subjects) - 1;


          $translate = '';
          if ($action->type == 'friends') {
            $translate = $this->translate(array('%1$s other people', '%1$s other peoples', $other_num), $other_num);
          } else if ($action->type == 'like_item_private') {
            $translate = $this->translate(array('%1$s other page', '%1$s other pages', $other_num), $other_num);
          }
          $other_link = '<a href="javascript:void(0);" class="wall_grouped_other">' . $translate . '</a>';

          $items_str = '';
          $count = count($action->grouped_subjects);
          if ($count > 2) {
            for ($i = 0; $i < $count; $i++) {
              if ($i == 0) {
                continue;
              }
              $subject = $action->grouped_subjects[$i];
              $items_str .= '<a href="' . $subject->getHref() . '" class="wall_liketips" rev="' . $subject->getGuid() . '">' . $subject->getTitle() . '</a>';
              if ($i < $count - 1) {
                $items_str .= ', ';
              }
            }
          } else {
            $subject = $action->grouped_subjects[1];
            $other_link = '<a href="' . $subject->getHref() . '" class="wall_liketips" rev="' . $subject->getGuid() . '">' . $subject->getTitle() . '</a>';
          }

          $translate_key = '';
          if ($action->type == 'friends') {
            $translate_key = '%1$s is now friends with %2$s and %3$s';
          } else if ($action->type == 'like_item_private') {
            $translate_key = '%1$s likes %2$s and %3$s';
          }

          $first_item_link = $this->htmlLink($first_item->getHref(), $first_item->getTitle(), array('class' => 'wall_liketips', 'rev' => $first_item->getGuid()));

          echo $this->translate($translate_key, array($profile_url, $first_item_link, $other_link));

          ?>

          <div style="display:none;" class="wall_grouped_other_html">
            <?php echo $items_str ?>
          </div>

        </span>


      </div>

    <?php else : ?>

      <div class="wall-menu-container">

        <span class="wall-menu-expand-link">
          <span class="wall_arrow"></span>
        </span>

        <ul class="wall-menu">
          <li><a href="javascript:void(0);"
                 onclick="Wall.showLink('<?php echo Engine_Api::_()->wall()->getHostUrl() . $action->getHref(); ?>')"
                 class="item wall_blurlink"><?php echo $this->translate('WALL_MENU_Link to this post'); ?></a></li>
          <li><a
              href="<?php echo $this->url(array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'subject' => $action->getGuid()), 'default', true); ?>"
              class="smoothbox item wall_blurlink"><?php echo $this->translate('WALL_MENU_Report Abuse'); ?></a></li>
          <?php if ($this->viewer()->getIdentity() && !$action->isOwner($this->viewer())): ?>
            <li><a href="javascript:void(0);"
                   class="item wall_blurlink wall_link_mute_action"><?php echo $this->translate('WALL_MENU_Mute this post'); ?></a>
            </li>
          <?php endif; ?>
          <?php if ($action->canRemoveTag($this->viewer()) && !$action->isOwner($this->viewer())): ?>
            <li><a href="javascript:void(0);"
                   class="item wall_blurlink wall_link_remove_tag"><?php echo $this->translate('WALL_MENU_Remove Tag'); ?></a>
            </li>
          <?php endif; ?>
        </ul>

      </div>


      <?php // User's profile photo ?>
      <div class='feed_item_photo'>

        <?php if (Engine_Api::_()->wall()->isOwnerTeamMember($action->getObject(), $action->getSubject())): ?>

          <?php echo $this->htmlLink($action->getObject()->getHref(),
            $this->itemPhoto($action->getObject(), 'thumb.icon', $action->getObject()->getTitle()), array('class' => 'wall_liketips', 'rev' => $action->getObject()->getGuid())) ?>

        <?php else : ?>

          <?php echo $this->htmlLink($action->getSubject()->getHref(),
            $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle()), array('class' => 'wall_liketips', 'rev' => $action->getSubject()->getGuid())) ?>

        <?php endif;

        ?>

      </div>


      <div class='feed_item_body feed_item_body_<?php $_item = current($action->getAttachments())->item; echo $_item ? strtolower($_item->getModuleName()) . '_' . $_item->getType() : 'none' ?>'>


      <?php // Main Content ?>
      <span
        class="<?php echo(empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated') ?>">

        <?php
        $moduleTable = Engine_Api::_()->getDbTable('modules', 'core');
        if ($moduleTable->isModuleEnabled('hashtag')) {
          $url = $this->url(array('module' => 'hashtag', 'controller' => 'index', 'action' => 'search'), 'default', true);
          $asd = $action->getContent();

            if(count($hasgtags)>0){
            foreach($hasgtags as $tag){
              if($tag['resource_id'] == $action->getIdentity()){
                if ($this->name == $tag['hashtag']) {
                  $asd = str_ireplace('#'.$tag['hashtag'], '<a href="javascript:void(0)" onClick="click_hashtags(\'' . $url . '\',\'' . $tag['hashtag'] . '\',\''.$this->translate("WALL_RECENT").'\',\'\',\''.$action->action_id.'\');" title="'.$this->translate('TITLE_LINK_HASHTAG').'" style="font-weight: bold;">' . '#'.$tag['hashtag'] . '</a>', $asd);
                } else {
                  $asd = str_ireplace('#'.$tag['hashtag'], '<a href="javascript:void(0)" onClick="click_hashtags(\'' . $url . '\',\'' . $tag['hashtag'] . '\',\''.$this->translate("WALL_RECENT").'\',\'\',\''.$action->action_id.'\');" title="'.$this->translate('TITLE_LINK_HASHTAG').' ">' . '#'.$tag['hashtag'] . '</a>', $asd);
                }
              }
          }
            }
          echo $asd;
        } else {
          echo $action->getContent();
        }
        ?>

        <?php

        $people_tags = $action->getPeopleTags();
        if (!empty($people_tags)) {

          $object_data = array();
          foreach ($people_tags as $item) {
            $object_data[] = array(
              'type' => $item->object_type,
              'id' => $item->object_id
            );
          }

          $object_fetch = Engine_Api::_()->wall()->getItems($object_data);
          $object_count = count($object_fetch);

          $sy = ' &mdash; ';
          if (empty($action->body)) {
            $sy = ' ' . $this->translate('WALL_is') . ' ';
          }

          if ($object_count == 0) {

          } else if ($object_count == 1) {
            echo '<span class="wall_with_people">' . $sy . '' . $this->translate('WALL_with %1$s', '<a href="' . $object_fetch[0]->getHref() . '" class="wall_liketips" rev="' . $object_fetch[0]->getGuid() . '">' . $object_fetch[0]->getTitle() . '</a>') . '</span>';
          } else if ($object_count == 2) {
            echo '<span class="wall_with_people">' . $sy . '' . $this->translate('WALL_with %1$s and %2$s', '<a href="' . $object_fetch[0]->getHref() . '" class="wall_liketips" rev="' . $object_fetch[0]->getGuid() . '">' . $object_fetch[0]->getTitle() . '</a>', '<a href="' . $object_fetch[1]->getHref() . '" class="wall_liketips" rev="' . $object_fetch[1]->getGuid() . '">' . $object_fetch[1]->getTitle() . '</a>') . '</span>';
          } else if ($object_count > 2) {

            $object_title = array();
            foreach ($object_fetch as $item) {
              $object_title[] = $item->getTitle();
            }

            echo '<span class="wall_with_people">' . $sy . '' . $this->translate('WALL_with %1$s and %2$s', '<a href="' . $object_fetch[0]->getHref() . '" class="wall_liketips" rev="' . $object_fetch[0]->getGuid() . '">' . $object_fetch[0]->getTitle() . '</a>', '<a href="javascript:void(0);" class="wall_tips" title="' . $this->wallFluentList($object_title) . '">' . $this->translate('WALL_%1$s others', $object_count) . '</a>') . '</span>';
          }
        }

        ?>




      </span>

      <?php // Attachments ?>
      <?php if ($action->getTypeInfo()->attachable && $action->attachment_count > 0): // Attachments ?>
        <div class='feed_item_attachments'>
          <?php if ($action->attachment_count > 0 && count($action->getAttachments()) > 0): ?>
            <?php if (count($action->getAttachments()) == 1 && current($action->getAttachments())->item &&
              null != ($richContent = current($action->getAttachments())->item->getRichContent())
            ): ?>
              <?php echo $richContent; ?>
            <?php else: ?>
              <?php foreach ($action->getAttachments() as $attachment): ?>
                <span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
                <?php if ($attachment->meta->mode == 0): // Silence ?>


                <?php elseif ($attachment->meta->mode == 1): // Thumb/text/title type actions ?>
                  <div>
                    <?php
                    if ($attachment->item->getType() == "core_link") {
                      $attribs = Array('target' => '_blank');
                    } else {
                      $attribs = Array();
                      $attribs['class'] = 'wall_liketips';
                      $attribs['rev'] = $attachment->item->getGuid();
                    }

                    ?>
                    <?php if ($attachment->item->getPhotoUrl()): ?>
                      <?php

                      $picture_type = 'thumb.normal';
                      if ($attachment->item->getType() == 'album_photo') {
                        $picture_type = 'thumb.profile';
                      }
                      ?>
                      <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, $picture_type, $attachment->item->getTitle()), $attribs) ?>
                    <?php endif; ?>
                    <div>
                      <div class='feed_item_link_title'>
                        <?php
                        echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? strip_tags($attachment->item->getTitle()) : '', $attribs);
                        ?>
                      </div>
                      <div class='feed_item_link_desc'>
                        <?php
                        //echo $attachment->item->getType().'<br>';
                        if ($attachment->item->getType() == 'forum_topic') {
                          echo $this->viewMore($attachment->item->getDescription());

                        } elseif ($attachment->item->getType() == 'activity_action') {
                          echo $this->viewMore($attachment->item->getDescription());
                        } else {
                          echo $this->viewMore($attachment->item->getDescription());
                        }
                        /*else{
                          echo  strip_tags($this->viewMore($attachment->item->getDescription()));
                       }*/
                        ?>

                      </div>
                    </div>
                  </div>

                <?php elseif ($attachment->meta->mode == 2): // Thumb only type actions ?>

                  <div class="feed_attachment_photo">
                    <?php

                    $attribs = array();
                    $attribs['class'] = 'feed_item_thumb';


                    ?>
                    <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
                  </div>

                <?php elseif ($attachment->meta->mode == 3): // Description only type actions ?>
                  <?php echo $this->viewMore($attachment->item->getDescription()); ?>
                <?php elseif ($attachment->meta->mode == 4): // Multi collectible thingy (@ztodo) ?>
                <?php endif; ?>
                </span>
              <?php endforeach; ?>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php // Icon, time since, action links ?>
      <?php

      $icon_type = 'activity_icon_' . $action->type;
      list($attachment) = $action->getAttachments();
      if (is_object($attachment) && $action->attachment_count > 0 && $attachment->item):
        $icon_type .= ' item_icon_' . $attachment->item->getType() . ' ';
      endif;
      $canComment = ($action->getTypeInfo()->commentable &&
        $this->viewer()->getIdentity() &&
        Engine_Api::_()->authorization()->isAllowed($action->getObject(), null, 'comment') &&
        !empty($this->commentForm));
      ?>
      <div class='feed_item_date feed_item_icon <?php echo $icon_type ?>'>

      <ul>
      <?php $is_option = false; ?>
      <?php if ($canComment): ?>
        <?php if ($action->likes()->isLike($this->viewer())): ?>
          <li class="feed_item_option_unlike">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unlike'), array('class' => 'action-unlike')) ?>
          </li>
        <?php else: ?>
          <li class="feed_item_option_like">
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Like'), array('class' => 'action-like')) ?>
          </li>
        <?php endif; ?>
		<?php if ($wealthApi->isFollowed($action->action_id,$this->viewer())): ?>
          <li class="feed_item_option_unfollow">
              <span>·</span>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Unfollow'), array('class' => 'action-unfollow')) ?>
          </li>
        <?php else: ?>
          <li class="feed_item_option_follow">
              <span>·</span>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Follow'), array('class' => 'action-follow')) ?>
          </li>
        <?php endif; ?>
		
        <?php if (Engine_Api::_()->getApi('settings', 'core')->core_spam_comment): // Comments - likes ?>
          <li class="feed_item_option_comment">
            <span>&#183;</span>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'index', 'action' => 'viewcomment', 'action_id' => $action->getIdentity(), 'format' => 'smoothbox'), $this->translate('Comment'), array(
              'class' => 'smoothbox',
            )) ?>
          </li>
        <?php else: ?>
          <li class="feed_item_option_comment">
            <span>&#183;</span>
            <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Comment'), array('class' => 'action-comment')) ?>
          </li>
        <?php endif; ?>

        <?php $is_option = true; ?>
      <?php endif; ?>


      <?php if ($this->viewer()->getIdentity() && (
        $this->activity_moderate || (
          $this->allow_delete && (
            ('user' == $action->subject_type && $this->viewer()->getIdentity() == $action->subject_id) ||
              ('user' == $action->object_type && $this->viewer()->getIdentity() == $action->object_id)
          )
        )
      )
      ): ?>
        <li class="feed_item_option_delete">
          <span>&#183;</span>
          <a href="javascript:void(0);" class="action-remove"><?php echo $this->translate('Delete') ?></a>
        </li>
        <?php $is_option = true; ?>
      <?php endif; ?>


      <?php // Share ?>
      <?php if ($action->getTypeInfo()->shareable && $this->viewer()->getIdentity()): ?>
        <?php if ($action->getTypeInfo()->shareable == 1 && $action->attachment_count == 1 && ($attachment = $action->getFirstAttachment())): ?>
          <li class="feed_item_option_share">
            <span>&#183;</span>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'index', 'action' => 'share', 'type' => $attachment->item->getType(), 'id' => $attachment->item->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
          </li>
        <?php elseif ($action->getTypeInfo()->shareable == 2): ?>
          <li class="feed_item_option_share">
            <span>&#183;</span>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'index', 'action' => 'share', 'type' => $subject->getType(), 'id' => $subject->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
          </li>
        <?php elseif ($action->getTypeInfo()->shareable == 3): ?>
          <li class="feed_item_option_share">
            <span>&#183;</span>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'index', 'action' => 'share', 'type' => $object->getType(), 'id' => $object->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
          </li>
        <?php elseif ($action->getTypeInfo()->shareable == 4): ?>
          <li class="feed_item_option_share">
            <span>&#183;</span>
            <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'index', 'action' => 'share', 'type' => $action->getType(), 'id' => $action->getIdentity(), 'format' => 'smoothbox'), $this->translate('Share'), array('class' => 'smoothbox', 'title' => 'Share')) ?>
          </li>
        <?php endif; ?>
        <?php $is_option = true; ?>
      <?php endif; ?>
      <li class="feed_item_option_date">
        <?php if ($is_option) : ?>
          <span>&#183;</span>
        <?php endif; ?>
        <a href="<?php echo $action->getHref(); ?>"><?php echo $this->timestamp($action->getTimeValue()) ?></a>
      </li>
      <?php if (isset($checkins[$action->getIdentity()]) && ($checkin = $checkins[$action->getIdentity()])): ?>
        <?php if ($pageEnabled && $checkin->object_type == 'page'): ?>
          <?php $page = Engine_Api::_()->getItem($checkin->object_type, $checkin->object_id); ?>
          <li class="feed_item_option_is_checkin">
            <p style="padding-left: 2px"><?php echo $this->translate('at ') ?><a class="feed_item_checkin wall_tips"
                                                                                 href="<?php echo $page->getHref() ?>"
                                                                                 title="<?php echo $page->country . ', ' . $page->city . ', ' . $page->street ?>"
                                                                                 target="_blank"><?php echo $page->getTitle() ?></a>
            </p>
          </li>
        <?php elseif ($eventEnabled && $checkin->object_type == 'event') : ?>
          <?php $event = Engine_Api::_()->getItem($checkin->object_type, $checkin->object_id); ?>
          <li class="feed_item_option_is_checkin">
            <p style="padding-left: 2px"><?php echo $this->translate('at ') ?><a class="feed_item_checkin wall_tips"
                                                                                 href="<?php echo $event->getHref() ?>"
                                                                                 title="<?php echo $checkin->vicinity ?>"
                                                                                 target="_blank"><?php echo $event->getTitle() ?></a>
            </p>
          </li>
        <?php else : ?>
          <li class="feed_item_option_is_checkin">
            <p style="padding-left: 2px"><?php echo $this->translate($this->wallLocationTypes($checkin->types)) ?><a
                href="<?php echo $this->url(array('module' => 'checkin', 'controller' => 'index', 'action' => 'view-map', 'place_id' => $checkin->place_id), 'default', true) ?>"
                class="smoothbox feed_item_checkin wall_tips"
                title="<?php echo $checkin->vicinity ?>"><?php echo $checkin->name ?></a></p>
          </li>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (!empty($action->params) && is_array($action->params) && !empty($action->params['is_mobile'])): ?>
        <li class="feed_item_option_is_mobile">
          <span>&#183;</span>
          <a href="javascript:void(0);" class="wall_tips wall-mobile"
             title="<?php echo $this->translate('WALL_IS_MOBILE') ?>"></a>
        </li>
      <?php endif;



      
      if ($action->canChangePrivacy($this->viewer())):

        $privacy_type = $action->object_type;
        $privacy = array();
        $privacy_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.privacy.disabled', ''));
        foreach (Engine_Api::_()->wall()->getPrivacy($privacy_type) as $item) {
          if (in_array($privacy_type . '_' . $item, $privacy_disabled)) {
            continue;
          }
          $privacy[] = $item;
        }

        $privacy_active = (empty($privacy[0])) ? null : $privacy[0];
        if (!empty($this->privacy_list[$action->action_id]) && in_array($this->privacy_list[$action->action_id], $privacy)) {
          $privacy_active = $this->privacy_list[$action->action_id];
        }


        $privacy_tips = $this->translate('WALL_PRIVACY_' . strtoupper($privacy_type) . '_' . strtoupper($privacy_active));

        $tagged_users = array();

        foreach ($people_tags as $item) {
          if ($item->object_type == 'user') {
            $tagged_users[] = $item;
          }
        }
        foreach ($action->getTags() as $item) {
          if ($item->object_type == 'user') {
            $tagged_users[] = $item;
          }
        }


        if ($privacy_active != 'everyone' && !$privacy_active != 'registered') {
          if (!empty($tagged_users)) {
            $privacy_tips .= $this->translate('WALL_' . strtoupper($privacy_type) . '_' . strtoupper($privacy_active) . '_TAGGED');
          }
        }
       endif;
        if ($moduleTable->isModuleEnabled('hashtag')) {

          $tagTable = Engine_Api::_()->getDbTable('tags', 'hashtag');
          $tTName = $tagTable->info('name');
          // print_die('test');
          $mapsTable = Engine_Api::_()->getDbTable('maps', 'hashtag');
          $mTName = $mapsTable->info('name');


          $select = $tagTable->select()
            ->from(array('t' => $tTName))
            ->joinLeft(array('m' => $mTName), 't.map_id = m.map_id', array())
            ->where('m.resource_id = ?', $action->getIdentity());


          $all = $tagTable->fetchAll($select);
          $id_hash = rand(10000, 99999999999);
          $count = count($all);
          if(count($all)>0){


        ?>
            <div  class="link_hashtag_div">
            <a href="javascript:void(0)" id='link_hashtag' class="hash_link_<?php echo $id_hash?> link_hash_tag"
              <?php if($count > 1){?>
               onmouseover="javascript:click_hashtags_menu('<?php echo $id_hash ?>','<?php echo $action->action_id ?>')"
               onClick="javascript:click_hashtags_menu_clikc('<?php echo $id_hash ?>','<?php echo $action->action_id ?>');          click_hashtags('<?php echo $url ?>','<?php echo $all[0]['hashtag'];?>','<?php echo $this->translate("WALL_RECENT")?>','','<?php echo $action->action_id ?>');" title="<?php echo $this->translate('TITLE_LINK_HASHTAG')?>"
              <?php
              }else{
              ?>
                onClick="javascript:  click_hashtags('<?php echo $url ?>','<?php echo $all[0]['hashtag'];?>','<?php echo $this->translate("WALL_RECENT")?>','','<?php echo $action->action_id ?>');" title="<?php echo $this->translate('TITLE_LINK_HASHTAG')?>"
              <?php }?>
               >#<?php echo $all[0]['hashtag'];?></a>
            </div>
        <?php
        }
        }
        if ($action->canChangePrivacy($this->viewer())):
        if ($privacy_active && count($privacy) > 1):

          ?>
          <li class="feed_item_option_privacy">

            <div class="wall-privacy-container">

              <div>&#183;</div>

              <div><a href="javascript:void(0);"
                      class="wall-privacy-action-link wall_blurlink wall_tips <?php if ($privacy_active == 'everyone'): ?>wall_is_public<?php endif; ?>"
                      title="<?php echo $privacy_tips; ?>">&nbsp;</a></div>
              <ul class="wall-privacy">
                <?php foreach ($privacy as $item): ?>
                  <li>
                    <a href="javascript:void(0);"
                       class="item wall_blurlink <?php if ($item == $privacy_active): ?>is_active<?php endif; ?>"
                       rev="<?php echo $item ?>">
                      <span class="wall_icon_active">&nbsp;</span>
                          <span class="wall_text">
                            <?php echo $this->translate('WALL_PRIVACY_' . strtoupper($privacy_type) . '_' . strtoupper($item)); ?>
                          </span>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
              <input type="hidden" name="privacy" value="<?php echo $privacy_active; ?>" class="wall_privacy_input"/>
              <input type="hidden" name="privacy_type" value="<?php echo $privacy_type; ?>"
                     class="wall_privacy_input_type"/>
              <input type="hidden" name="privacy_tag_active"
                     value="<?php if (empty($tagged_users)): ?><?php else : ?>1<?php endif; ?>"
                     class="wall_privacy_tag_active"/>
            </div>

          </li>

        <?php endif; ?>

      <?php endif; ?>
      </ul>

      <?php

      if ($moduleTable->isModuleEnabled('hashtag')) {



        if (count($all) > 1) {
          ?>


          <div class="box_for_hashtag display_none" id='hash<?php echo $id_hash ?>'  onmouseout="javascript:mous_out_div()" >
            <?php
            $b=1;
            foreach ($all as $tags) {
              if($b!=1){
              if ($tags['hashtag'] == '') break;
              if ($this->name == $tags['hashtag']) {
                echo '<a href="javascript:void(0)"  onClick="click_hashtags(\'' . $url . '\',\'' . $tags['hashtag'] . '\',\''.$this->translate("WALL_RECENT").'\',\'\',\''.$action->action_id.'\');" title="'.$this->translate('TITLE_LINK_HASHTAG').'"  class="link_tag_select" style="">#' . $tags['hashtag'] . '</a> ';

              } else {
                echo '<a href="javascript:void(0)"  onClick="click_hashtags(\'' . $url . '\',\'' . $tags['hashtag'] . '\',\''.$this->translate("WALL_RECENT").'\',\'\',\''.$action->action_id.'\');" title="'.$this->translate('TITLE_LINK_HASHTAG').'" class="link_tag">#' . $tags['hashtag'] . '</a> ';
              }
              }
              $b++;

            }
            ?>
          </div>

        <?php

        }
      }
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      ?>

      </div>

      <div class='comments wall-comments'>
        <ul>
          <?php
          $this->action = $action;
          ?>
          <?php echo $this->render('_comments.tpl') ?>
        </ul>
        <?php if ($canComment):
          echo $this->commentForm
            ->setActionIdentity($this->action->action_id)
            ->render();
        endif; ?>
      </div>

      </div>


    <?php endif; ?>
    <div class="clr"></div>

    <?php if (!$this->noList): ?></li><?php endif; ?>

    <?php
    ob_end_flush();
  } catch (Exception $e) {
    ob_end_clean();
    if (APPLICATION_ENV === 'development') {
      echo $e->__toString();
    }
  };
  ?>





<?php

endforeach;

?>

