<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _formSignupImage.tpl 9696 2012-04-20 20:17:37Z richard $
 * @author     Jung
 */
?>

<?php
  if (isset($_SESSION['TemporaryProfileImg'])){
?>
  <div data-role="navbar" data-grid="d" class="component-gallery">
    <ul class="thumbs">
      <li>
        <a class="thumbs_photo" href="<?php echo $_SESSION['TemporaryProfileImg'] ?>">
          <span style="background-image: url('<?php echo $_SESSION['TemporaryProfileImgProfile'] ?>');"></span>
        </a>
      </li>
    </ul>
  </div>
<?php }?>
<div>
  <?php
    $settings = Engine_Api::_()->getApi('settings', 'core');

    if (isset($_SESSION['TemporaryProfileImg']) && $settings->getSetting('user.signup.photo', 0) == 1){
      echo '<button name="done" id="done" type="submit" onclick="javascript:finishForm();">Save Photo</button>';
    }
  ?>
</div>


