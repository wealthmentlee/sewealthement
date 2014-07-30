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
?>


<?php
echo $this->render('_header.tpl');
?>



<?php $signup_url = $this->htmlLink($this->url(array(), 'user_signup'), $this->translate("Sign Up")); ?>
<?php $login_url = $this->htmlLink($this->url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), 'user_login'), $this->translate("Sign In")); ?>
<script type="text/javascript">
  var is_enabled = '<?php echo $this->is_enabled; ?>';
  var openLogin = function() {
    if (!is_enabled) {
      Smoothbox.open('<center><?php echo $this->translate('like_%s or %s', $login_url, $signup_url); ?></center>');
    } else {
      Smoothbox.open(<?php echo $this->jsonInline('<center style="margin-top: 20px; font-weight: bold; color: red;">'.$this->translate('LIKE_PERMISSION_TO_USE_LIKE'). '</center>')?>);
    }
  }


  window.wall_like = function (element, type, id){

    $(element).getParent('li').addClass('wall_liked');

    Wall.request(en4.core.baseUrl + 'like/like', {
      object: type,
      object_id: id
    });
  };

  window.wall_unlike = function (element, type, id){

    $(element).getParent('li').removeClass('wall_liked');

    Wall.request(en4.core.baseUrl + 'like/unlike', {
      object: type,
      object_id: id
    });
  };



</script>


<div class="wall_most_likes">
  <ul>
    <?php

    foreach ($this->items as $item):

      $auth = ( $item->authorization()->isAllowed(null, 'view') );
      $is_like = Engine_Api::_()->getDbTable('likes', 'core')->isLike($item, $this->viewer());

      if ($auth):?>

      <li class="<?php if ($is_like):?>wall_liked<?php endif;?>">
        <div class="item_photo">
          <a href="<?php echo $item->getHref();?>"><?php echo $this->itemPhoto($item, 'thumb.profile');?></a>
        </div>
        <div class="item_body">
          <div class="item_title">
            <a href="<?php echo $item->getHref()?>"><?php echo $item->getTitle();?></a>
          </div>
          <div class="item_description">
            <?php
              if (!empty($this->like_count) && !empty($this->like_count[$item->getGuid()])):
                $like_count = $this->like_count[$item->getGuid()];
             ?>
              <?php echo $this->translate(array('%s like', '%s likes', $like_count), $like_count)?>

            <?php endif;?>
          </div>
        </div>
        <div class="item_options">
          <a href="javascript:void(0);" class="wall_like wall-button" onclick="window.wall_like(this, '<?php echo $item->getType();?>', <?php echo $item->getIdentity()?>)"><span class="wall_icon">&nbsp;</span><?php echo $this->translate('WALL_LIKE') ?></a>
          <a href="javascript:void(0);" class="wall_unlike wall-button" onclick="window.wall_unlike(this, '<?php echo $item->getType();?>', <?php echo $item->getIdentity()?>)"><span class="wall_icon">&nbsp;</span><?php echo $this->translate('WALL_UNLIKE') ?></a>
        </div>
      </li>

      <?php endif;?>

    <?php endforeach ;?>
  </ul>
</div>