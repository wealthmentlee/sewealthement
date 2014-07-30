<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       14.08.12
 * @time       14:04
 */?>

<div id='profile_status'>
  <h2 class="like_status_header">
    <?php if($this->subject()->type == 'fundraise') :?>
      <?php echo  $this->htmlLink($this->subject->getOwner()->getHref(), $this->subject->getOwner());?>:
    <?php endif;?>
    <?php echo $this->subject()->getTitle() ?>
  </h2>
  <?php if ($this->is_enabled && $this->is_allowed): ?>
    <?php echo $this->likeButton($this->subject); ?>
  <?php else : ?>
    <?php $signup_url = $this->htmlLink($this->url(array(), 'user_signup'), $this->translate("Sign Up")); ?>
    <?php $login_url = $this->htmlLink($this->url(array('return_url' => '64-' . base64_encode($_SERVER['REQUEST_URI'])), 'user_login'), $this->translate("Sign In")); ?>
    <script type="text/javascript">
      var is_enabled = '<?php echo $this->is_enabled; ?>';
      var openLogin = function() {
        if (!is_enabled) {
          Smoothbox.open('<center><?php echo $this->translate('like_%s or %s', $login_url, $signup_url); ?></center>');
        }else {
          Smoothbox.open('<center style="margin-top: 20px; font-weight: bold; color: red;"><?php echo addslashes($this->translate('LIKE_PERMISSION_TO_USE_LIKE')); ?></center>');
        }
      }
    </script>
    <?php echo '<a href="javascript:void(0)" class="like_button_link" onclick="openLogin()"><span class="like_button">'.$this->translate('like_Like').'</span></a>'; ?>
  <?php endif; ?>
  <div class="clr"></div>
</div>

