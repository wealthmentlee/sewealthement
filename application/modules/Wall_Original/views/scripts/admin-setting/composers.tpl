<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: composers.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php


  $composers = array();
  foreach (Engine_Api::_()->wall()->getManifestType('wall_composer') as $key => $value){
    if (empty($value['can_disable'])){
      continue ;
    }
    $composers[] = $key;
  }
  $composers_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.composers.disabled', 'smile'));



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

    <li>
      <ul>

      <?php

      foreach ($composers as $key){
        ?>
        <li <?php if (in_array($key, $composers_disabled)):?>class="disabled"<?php endif;?>>
          <div class="item_checked">
            <input type="checkbox" name="composers_enabled[<?php echo $key?>]" value="1" class="make_enabled" <?php if (!in_array($key, $composers_disabled)):?>checked="checked" <?php endif;?>/>
          </div>
          <div class="item_title">
            <?php echo $this->translate('WALL_COMPOSERS_'.strtoupper($key))?>
          </div>
        </li>
        <?php

      }
      ?>

    </ul></li>



  </ul>

</div>