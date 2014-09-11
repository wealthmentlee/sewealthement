<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php if (!$this->action):?>
  <h2><?php echo $this->translate("Activity Item Not Found") ?></h2>
  <p>
    <?php echo $this->translate("The page you have attempted to access could not be found.") ?>
  </p>
<?php return ; endif;?>





<?php

// includes translations, runonce javascript
echo $this->render('_header.tpl')
?>

<script type="text/javascript">

  Wall.runonce.add(function (){

    var feed = new Wall.Feed({
      feed_uid: '<?php echo $this->feed_uid?>',
      enableComposer: <?php echo ($this->enableComposer) ? 1 : 0?>,
      url_wall: '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'wall.feed'), 'default', true) ?>',
      last_id: <?php echo sprintf('%d', $this->firstid) ?>,
      subject_guid : '<?php echo $this->subjectGuid ?>'
    });
        
  });

</script>

<div class="wall-view-action-title">
  <div class="item_photo">
    <?php if (Engine_Api::_()->wall()->isOwnerTeamMember($this->action->getObject(), $this->action->getSubject())): ?>
    <a href="<?php echo $this->action->getObject()->getHref();?>" class="feed_item_username wall_liketips" rev="<?php echo $this->action->getObject()->getGuid();?>"><?php echo $this->itemPhoto($this->action->getObject(), 'thumb.icon');?></a>
    <?php else :?>
    <a href="<?php echo $this->action->getSubject()->getHref();?>" class="feed_item_username wall_liketips" rev="<?php echo $this->action->getSubject()->getGuid();?>"><?php echo $this->itemPhoto($this->action->getSubject(), 'thumb.icon');?></a>
    <?php endif;?>
  </div>
  <div class="item_title">
    <?php if (Engine_Api::_()->wall()->isOwnerTeamMember($this->action->getObject(), $this->action->getSubject())): ?>
    <a href="<?php echo $this->action->getObject()->getHref();?>" class="feed_item_username wall_liketips" rev="<?php echo $this->action->getObject()->getGuid();?>"><?php echo $this->action->getObject()->getTitle();?></a>
    <?php else: ?>
    <a href="<?php echo $this->action->getSubject()->getHref();?>" class="feed_item_username wall_liketips" rev="<?php echo $this->action->getSubject()->getGuid();?>"><?php echo $this->action->getSubject()->getTitle();?></a>
    <?php endif;?>
    <div>
      <ul>
        <li class="feed_item_option_date">
          <a href="<?php echo $this->action->getHref();?>"><?php echo $this->timestamp($this->action->getTimeValue()) ?></a>
        </li>
      </ul>
    </div>
  </div>
</div>

<div class="wallFeed" id="<?php echo $this->feed_uid?>">

  <ul class="wall-feed feed" id="activity-feed">
    <li rev="item-<?php echo $this->action->action_id ?>" class="wall-action-item <?php if ($this->comment_pagination):?>action_comment_pagination<?php endif;?>" >
      <?php echo $this->wallActivity($this->action, array('noList' => true, 'comment_pagination' => $this->comment_pagination, 'comment_page' => $this->comment_page, 'viewAllLikes' => $this->viewAllLikes)); ?>
    </li>
  </ul>

</div>