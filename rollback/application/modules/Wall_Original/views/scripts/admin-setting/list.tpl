<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: list.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>


<?php

  $options = array();

  $default = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.default', 'most_recent');
  $list_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.disabled'));


  foreach (Engine_Api::_()->wall()->getManifestType('wall_type') as $key => $type){
    $options[$key] = array(
      'label' => 'WALL_ADMIN_LIST_' . strtoupper($key),
      'default' => ($default === $key),
      'disabled' => (in_array($key, $list_disabled))
    );
  }


?>

<script type="text/javascript">

  en4.core.runonce.add(function (){

    $$('.wall_list_lists .make_list_enabled').addEvent('change', function (){

      if ($(this).checked){
        $(this).getParent('li').removeClass('disabled');
      } else {
        $(this).getParent('li').addClass('disabled');
      }

      if ($(this).getParent('li').getElement('.list_default').get('value')){
        $$('.wall_list_lists .list_default').set('value', '');
        $$('.wall_list_lists li').removeClass('default');
        $(this).getParent('li').getElement('.list_default').set('value', 0);
        $(this).getParent('li').removeClass('default');
      }

    });

    $$('.wall_list_lists .make_list_default').addEvent('click', function (){

      if ($(this).getParent('li').hasClass('disabled')){
        return ;
      }

      $$('.wall_list_lists .list_default').set('value', '');
      $$('.wall_list_lists li').removeClass('default');
      $(this).getParent('li').getElement('.list_default').set('value', 1);
      $(this).getParent('li').addClass('default');

    });

    $$('.wall_list_lists .make_list_default_span').addEvent('click', function (){

      if ($(this).getParent('li').hasClass('disabled')){
        return ;
      }

      $$('.wall_list_lists .list_default').set('value', '');
      $$('.wall_list_lists li').removeClass('default');
      $(this).getParent('li').getElement('.list_default').set('value', 0);
      $(this).getParent('li').removeClass('default');

    });



  });

</script>

<ul class="wall_list_lists">
  <?php foreach ($options as $key => $list):?>
    <li rev="<?php echo $key?>" class="list <?php if ($list['default']):?>default<?php endif;?> <?php if ($list['disabled']):?>disabled<?php endif;?>">
      <div class="list-checked">
        <input type="checkbox" name="list_enabled[<?php echo $key?>]" value="1" class="make_list_enabled" <?php if (!$list['disabled']):?>checked="checked"<?php endif;?>>
      </div>
      <div class="list-label">
        <?php echo $this->translate($list['label']); ?>
      </div>
      <div class="list-options">
        <a href="javascript:void(0);" class="make_list_default">
          <?php echo $this->translate('WALL_ADMIN_LIST_MAKE_DEFAULT')?>
        </a>
        <a href="javascript:void(0);" class="make_list_default_span">
          <?php echo $this->translate('WALL_ADMIN_LIST_UNMAKE_DEFAULT')?>
        </a>


        <input type="hidden" name="list_default[<?php echo $key?>]" class="list_default" value="<?php if (!$list['disabled'] && $list['default']):?>1<?php else:?><?php endif;?>" />
      </div>
    </li>
  <?php endforeach ; ?>
</ul>