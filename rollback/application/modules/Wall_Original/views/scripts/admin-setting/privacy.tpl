<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: privacy.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>


<?php

$privacy = Engine_Api::_()->wall()->getPrivacyList();
$privacy_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.privacy.disabled'));

?>


<script type="text/javascript">

  en4.core.runonce.add(function (){

    $$('.wall_admin_table .make_enabled').addEvent('change', function (){

      if ($(this).checked){
        $(this).getParent('li').removeClass('disabled');
      } else {
        $(this).getParent('li').addClass('disabled');
      }

    });

  });

</script>

<div>
  <ul class="wall_admin_table">
    <?php

    ?>
    <?php foreach ($privacy as $key => $item):?>

      <li>

        <div class="wall_category">
          <?php echo $this->translate('WALL_ADMIN_PRIVACY_CATEGORY_' . strtoupper($key));?>
        </div>

        <ul>

        <?php foreach ($item as $pes):?>
          <?php
            $str = 'WALL_PRIVACY_' . strtoupper($key) . '_'  . strtoupper($pes);
            $privacy_key = $key . '_' . $pes;
          ?>
          <li <?php if (in_array($privacy_key, $privacy_disabled)):?>class="disabled"<?php endif;?>>
            <div class="item_checked">
              <input type="checkbox" name="privacy_enabled[<?php echo $privacy_key?>]" value="1" class="make_enabled" <?php if (!in_array($privacy_key, $privacy_disabled)):?>checked="checked" <?php endif;?>/>
            </div>
            <div class="item_title">
              <?php echo $this->translate($str);?>
            </div>
            <div class="item_options">
              <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'language', 'action' => 'edit', 'locale' => 'en', 'search' => $str), 'admin_default', true)?>">
                <?php echo $this->translate('WALL_EDIT_TRANSLATE')?>
              </a>
            </div>
          </li>
          <?php endforeach ;?>

          </ul>

      </li>

    <?php endforeach ;?>
  </ul>
</div>