<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: items.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php if (empty($this->stream)):?>

  <?php
    if ($this->viewall){
      echo '<script type="text/javascript">Wall.runonce.add(function (){ Wall.dialog.message(en4.core.language.translate("WALL_STREAM_EMPTY_VIEWALL"), 2); });</script>';
      return ;
    }
  ?>

  <?php if (empty($this->getUpdate)):?>
    <li>
      <div class="tip">
        <span>
          <?php echo $this->translate('WALL_STREAM_EMPTY')?>
        </span>
      </div>
    </li>
  <?php endif;?>

<?php return ; endif ;?>


<?php foreach ($this->stream as $action):?>

  <li class="wall_twitter_item <?php if (!empty($action['favorited'])):?>wall_favorited<?php endif;?> <?php if (!empty($action['retweeted'])):?>wall_retweeted<?php endif;?>">

    <span class="wall_status_icon">&nbsp;</span>

    <div class="item_photo">
      <a href="<?php echo $action['user']['url']?>" target="_blank"><img src="<?php echo $action['user']['profile_image_url']?>" alt=""/></a>
    </div>

    <div class="item_body">
      <div class="item_title">
        <span class="screen_name"><?php echo $action['user']['screen_name'];?></span>
        <span class="name"><?php echo $action['user']['name'];?></span>
      </div>
      <div class="item_text">

        <?php

        $text = $action['text'];
        $text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\" rel='nofollow'>\\2</a>", $text);
        $text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\" rel='nofollow'>\\2</a>", $text);
        $text = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\" rel='nofollow'>@\\1</a>", $text);
        $text = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\" rel='nofollow'>#\\1</a>", $text);
        echo $text;
?>

      </div>
      <div class="item_line">

        <div class="wall_item_icon"></div>
        <div class="item_date">
          <?php echo $this->timestamp($action['created_at']);?>
          <?php if (!empty($action['source'])):?>
            <?php echo $this->translate('WALL_TWITTER_VIA', array($action['source']))?>
          <?php endif;?>
        </div>


        <?php
          $can_delete = (@$action['user']['id'] == $this->token->object_id);
          $can_retweet = !(@$action['user']['id'] == $this->token->object_id);
        ?>

        <div class="item_options">
          <a href="javascript:void(0);" class="wall_tweet"><?php echo $this->translate('WALL_TWITTER_REPLY')?></a>
          <?php if ($can_retweet):?>
            <a href="javascript:void(0);" class="wall_retweet"><?php echo $this->translate('WALL_TWITTER_RETWEET')?></a>
          <?php endif;?>
          <span class="wall_unretweet"><?php echo $this->translate('WALL_TWITTER_RETWEETED')?></span>
          <a href="javascript:void(0);" class="wall_favorite"><?php echo $this->translate('WALL_TWITTER_FAVORITE')?></a>
          <a href="javascript:void(0);" class="wall_unfavorite"><?php echo $this->translate('WALL_TWITTER_UNFAVORITE')?></a>
          <?php if ($can_delete):?>
            <a href="javascript:void(0);" class="wall_delete"><?php echo $this->translate('WALL_TWITTER_DELETE')?></a>
          <?php endif;?>
        </div>




        <?php
        /*
         http://twitter.com/intent/tweet?in_reply_to=<?php echo $action['id_str']?>
        http://twitter.com/intent/retweet?tweet_id=<?php echo $action['id_str']?>
        http://twitter.com/intent/favorite?tweet_id=<?php echo $action['id_str']?>
        http://twitter.com/intent/favorite?tweet_id=<?php echo $action['id_str']?>
         */

        ?>

      </div>


      <?php if (!empty($action['in_reply_to_screen_name'])):?>
        <div class="wall_in_reply">
          <?php echo '<a href="http://www.twitter.com/'.$action['in_reply_to_screen_name'].'/status/'.$action['id_str'].'" target="_blank">' . $this->translate('WALL_TWITTER_IN_REPLY', array('<b>@'.$action['in_reply_to_screen_name'])) .'</b></a>';?>
        </div>
      <?php endif;?>



      <div class="wall-twitter-reply">
        <form action="" onsubmit="return false;">
          <div class="wall_title">
            <?php echo $this->translate('WALL_TWITTER_REPLY_TO', array('@'. $action['user']['screen_name'] . ''));?>
          </div>
          <textarea rows="1" cols="1" name="message"></textarea>
          <input type="hidden" name="start_message" value='@<?php echo $action['user']['screen_name']?> ' class="wall_start_message" />
          <input type="hidden" name="id" value="<?php echo $action['id_str']?>" />
          <div class="wall-submit-container">
            <div class="wall-submit-container-button">
              <span class="wall_counter">140</span>
              <button type="submit"><?php echo $this->translate('WALL_TWITTER_REPLY_SUBMIT')?></button>
            </div>
          </div>

        </form>
      </div>


      <span style="display: none;" class="wall_twitter_id"><?php echo $action['id_str']?></span>

    </div>

  </li>

<?php endforeach;?>


<?php if( empty($this->stream) ): ?>
  <?php if (empty($this->getUpdate)):?>
    <li class="utility-empty" style="display: none;">
      <div class="tip">
        <span>
          <?php
            if ($this->viewall){
              echo $this->translate("WALL_STREAM_EMPTY_VIEWALL");
            } else {
              echo $this->translate("WALL_STREAM_EMPTY");
            }

          ?>
        </span>
      </div>
    </li>
  <?php endif;?>
<?php endif;?>

<?php if ($this->show_viewall):?>
	<li class="utility-viewall">
	  <div class="pagination">
		<a href="javascript:void(0);" rev="item_<?php echo $this->next?>"><?php echo $this->translate('View More')?></a>
	  </div>
	  <div class="loader" style="display: none;">
		<div class="wall_icon"></div>
		<div class="text">
		  <?php echo $this->translate('Loading ...')?>
		</div>
	  </div>
	</li>
<?php endif;?>

<?php if ($this->since && !$this->viewall):?>
<li class="utility-setsince wall_displaynone" rev="<?php echo $this->since;?>"></li>
<?php endif;?>
