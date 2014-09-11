<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: tabs.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php

  $tabs = Engine_Api::_()->wall()->getManifestType('wall_tabs');
  $tabs_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.disabled'));

  $tab_default = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.tab.default', 'social');

?>


<script type="text/javascript">

  en4.core.runonce.add(function (){

    $$('.wall_tabs_content .make_enabled').addEvent('change', function (){

      if ($(this).checked){
        $(this).getParent('li').removeClass('disabled');
      } else {
        $(this).getParent('li').addClass('disabled');
      }

    });

    $$('.wall_tabs_content .make_tab_default').addEvent('click', function (){

      if ($(this).getParent('li').hasClass('disabled')){
        return ;
      }

      $$('.wall_tabs_content .tab_default').set('value', '');
      $$('.wall_tabs_content li').removeClass('default');
      $(this).getParent('li').getElement('.tab_default').set('value', 1);
      $(this).getParent('li').addClass('default');

    });

    $$('.wall_tabs_content .make_tab_default_span').addEvent('click', function (){

      if ($(this).getParent('li').hasClass('disabled')){
        return ;
      }

      $$('.wall_tabs_content .tab_default').set('value', '');
      $$('.wall_tabs_content li').removeClass('default');
      $$('.wall_tab_social')[0].getElement('.tab_default').set('value', 1);
      $$('.wall_tab_social')[0].addClass('default');


    });

  });

</script>

<div>

  <ul class="wall_admin_table wall_tabs_content">

    <li>
      <ul>

      <?php

      foreach ($tabs as $key => $tab){
        ?>
        <li class="wall_tab_<?php echo $key;?> <?php if (in_array($key, $tabs_disabled)):?>disabled<?php endif;?> <?php if ($tab_default == $key):?>default<?php endif;?>">
          <div class="item_checked">
            <input type="checkbox" name="tabs_enabled[<?php echo $key?>]" value="1" class="make_enabled" <?php if (!in_array($key, $tabs_disabled)):?>checked="checked" <?php endif;?>/>
          </div>
          <div class="item_title">
            <?php echo $this->translate('WALL_STREAM_'.strtoupper($key))?>
          </div>
          <div class="item_options">

            <input type="hidden" name="tab_default[<?php echo $key?>]" class="tab_default" value="<?php if (!in_array($key, $tabs_disabled) && $tab_default == $key):?>1<?php else:?><?php endif;?>" />

            <?php if ($key == 'social' || $key == 'welcome'):?>


              <span class="make_tab_default_container">

                <a href="javascript:void(0);" class="make_tab_default">
                  <?php echo $this->translate('WALL_ADMIN_TAB_MAKE_DEFAULT')?>
                </a>
                <a href="javascript:void(0);" class="make_tab_default_span">
                  <?php echo $this->translate('WALL_ADMIN_TAB_UNMAKE_DEFAULT')?>
                </a>

                &#183;

              </span>

            <?php endif;?>

            <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'language', 'action' => 'edit', 'locale' => 'en', 'search' => 'WALL_STREAM_'.strtoupper($key)), 'admin_default', true)?>"><?php echo $this->translate('WALL_EDIT_TRANSLATE');?></a>

          </div>
        </li>
        <?php

      }
      ?>

    </ul></li>



  </ul>

</div>