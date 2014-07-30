<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: item.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>
<?php

  $action = $this->action;
  if (empty($action)){
    return ;
  }

?>


<?php

$profile_url = "http://www.facebook.com/profile.php?id=" . $action['from']['id'];
$matches = explode("_", $action['id']);
$post_id = array_pop($matches);
$post_url = "http://www.facebook.com/{$action['from']['id']}/posts/$post_id";
$action_id = $action['id'];

$is_like = false;
if (!empty($this->token) && !empty($action['likes']) && !empty($action['likes']['data'])){
  foreach ($action['likes']['data'] as $item){
    if ($this->token->object_id == $item['id']){
      $is_like = true;
    }
  }
}


?>

<div class="item_photo">
  <a href="<?php echo $profile_url?>" target="_blank"><img src="https://graph.facebook.com/<?php echo $action['from']['id']?>/picture" alt=""/></a>
</div>
<div class="item_body">
  <div class="item_title">
      <span class="name">
        <a href="<?php echo $profile_url?>" target="_blank"><?php echo $action['from']['name']?></a>
      </span>

  </div>
  <div class="item_text">

    <div class="body">

      <?php if (!empty($action['story'])):?>
      <?php echo $this->viewMore($action['story']);?>
      <?php endif;?>

      <?php if (!empty($action['message'])):?>
      <?php echo $this->viewMore($action['message']);?>
      <?php endif;?>


    </div>

    <?php if ((!empty($action['picture'])) || (!empty($action['name']) || !empty($action['caption']) || !empty($action['description']))):?>

    <div class="attachment">

      <div class="media">

        <?php if (!empty($action['picture'])):?>

        <div class="media_photo">
          <a href="<?php echo $action['link']?>" rel="nofollow" target="_blank"><img src="<?php echo $action['picture']?>" alt="<?php echo (!empty($action['name'])) ? $action['name'] : ''?>"/></a>
        </div>

        <?php endif;?>

        <?php if (!empty($action['name']) || !empty($action['caption']) || !empty($action['description'])):?>
        <div class="media_content">
          <?php if (!empty($action['name'])):?>
          <div class="name"><a href="<?php echo $post_url?>" rel="nofollow" target="_blank"><?php echo $action['name']?></a></div>
          <?php endif;?>
          <?php if (!empty($action['caption'])):?>
          <div class="caption"><a href="<?php echo $post_url?>" rel="nofollow" target="_blank"><?php echo $action['caption']?></a></div>
          <?php endif;?>
          <?php if (!empty($action['description'])):?>
          <div class="description"><?php echo $this->viewMore($action['description'])?></div>
          <?php endif;?>
        </div>
        <?php endif;?>

      </div>

    </div>

    <?php endif;?>


  </div>
  <div class="item_line">

    <ul class="item_options">

      <?php if (!empty($action['actions'])):?>

      <?php
      $links = array();
      foreach ($action['actions'] as $item){
        $links[strtolower(@$item['name'])] = $item;
      }
      ?>

      <?php if (!empty($links['like'])):?>
        <li class="wall_facebook_likes">
          <a href="<?php echo $links['like']['link']?>" target="_blank" class="wall_facebook_likes_like <?php if (!$is_like):?>wall_active<?php endif;?>" rev="<?php echo $action_id;?>"><?php echo $this->translate('WALL_FACEBOOK_' . strtoupper('like'))?></a>
            <a href="<?php echo $links['like']['link']?>" target="_blank" class="wall_facebook_likes_unlike <?php if ($is_like):?>wall_active<?php endif;?>" rev="<?php echo $action_id;?>"><?php echo $this->translate('WALL_FACEBOOK_' . strtoupper('unlike'))?></a>
        </li>
        <li>&middot;</li>
        <?php endif;?>
      <?php if (!empty($links['comment'])):?>
        <li><a href="<?php echo $links['comment']['link']?>" target="_blank" class="wall_facebook_comment"><?php echo $this->translate('WALL_FACEBOOK_' . strtoupper('comment'))?></a></li>
        <li>&middot;</li>
        <?php endif;?>
      <?php if (!empty($links['share'])):?>
        <li><a href="<?php echo $links['share']['link']?>" target="_blank"><?php echo $this->translate('WALL_FACEBOOK_' . strtoupper('share'))?></a></li>
        <li>&middot;</li>
        <?php endif;?>

      <?php endif;?>

      <?php if ((!empty($action['comments']) && $action['comments']['count']) || (!empty($action['likes']) && $action['likes']['count'])):?>

      <?php if (!empty($action['comments']) && $action['comments']['count']):?>
        <li>
          <a href="<?php echo $post_url?>" target="_blank" class="count_container">
            <span class="count_comments"><?php echo $action['comments']['count']?></span>
          </a>
        </li>
        <?php endif;?>

      <?php if (!empty($action['likes']) && $action['likes']['count']):?>
        <li>
          <a href="<?php echo $post_url?>" target="_blank" class="count_container">
            <span class="count_likes"><?php echo $action['likes']['count']?></span>
          </a>
        </li>
        <?php endif;?>

      <li>&middot;</li>

      <?php endif;?>

      <li>
        <div class="item_date">
          <a href="<?php echo $post_url;?>" target="_blank"><?php echo $this->timestamp($action['updated_time']);?></a>
        </div>
      </li>

    </ul>

    <?php if ((!empty($action['comments']) && !empty($action['comments']['count'])) || (!empty($action['likes']) && !empty($action['likes']['count']))):?>
    <div class="comments wall-comments">
      <ul>
        <?php if (!empty($action['likes']) && !empty($action['likes']['count'])):?>
        <li class="container-comment_likes">
          <div></div>
          <div class="comments_likes">
            <a href="https://www.facebook.com/browse/likes/?id=<?php echo $post_id;?>">
              <?php echo $this->translate(array('%s person likes this', '%s people like this', $action['likes']['count']), $this->locale()->toNumber($action['likes']['count']));?>
            </a>
          </div>
        </li>
        <?php endif;?>

        <?php if (!empty($action['comments']) && !empty($action['comments']['count'])):?>
        <?php if( $action['comments']['count'] > 5): ?>
          <li>
            <div></div>
            <div class="comments_viewall">
              <?php if( $action['comments']['count'] > 2): ?>
              <a href="<?php echo $post_url;?>">
                <?php echo $this->translate(array('View all %s comment', 'View all %s comments', $action['comments']['count']), $this->locale()->toNumber($action['comments']['count']));?>
              </a>
              <?php endif; ?>
            </div>
          </li>
          <?php endif; ?>

        <?php endif; ?>

        <?php if (!empty($action['comments']['data'])):?>
        <?php foreach ($action['comments']['data'] as $comment):?>
          <?php
          $poster_name = '';
          $poster_photo = '';
          $poster_link = '';
          if (!empty($comment['from']) && !empty($comment['from']['name']) && !empty($comment['from']['id'])){
            $poster_name = $comment['from']['name'];
            $poster_link = "http://www.facebook.com/profile.php?id=" . $comment['from']['id'];
            $poster_photo = 'https://graph.facebook.com/'.$comment['from']['id'].'/picture';
          }
          ?>
          <li>
            <div class="comments_author_photo">
              <a href="<?php echo $poster_link;?>"><img src="<?php echo $poster_photo?>" alt=""/></a>
            </div>
            <div class="comments_info">
                    <span class="comments_author">
                      <a href="<?php echo $poster_link;?>"><?php echo $poster_name?></a>
                    </span>
              <?php echo $comment['message']; ?>
            </div>
          </li>
          <?php endforeach;?>
        <?php endif;?>
      </ul>
    </div>
    <?php endif;?>


    <div class="wall-facebook-comment">
      <form action="" onsubmit="return false;">
        <textarea rows="1" cols="1" name="message"></textarea>
        <input type="hidden" name="id" value="<?php echo $action_id?>" />
        <div class="wall-submit-container">
          <div class="wall-submit-container-button">
            <button type="submit"><?php echo $this->translate('WALL_FACEBOOK_COMMENT_SUBMIT')?></button>
          </div>
        </div>

      </form>
    </div>



  </div>
</div>

