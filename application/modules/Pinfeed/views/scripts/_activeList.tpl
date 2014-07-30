<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _activeList.tpl 18.06.12 10:52 michael $
 * @author     Bolot
 */
?>

<div class="fake-lists">
  <span class="pinfeed_loader"></span>
  <div class="wall-element-external">
<ul class="pinfeed_types" id="pinfeed_types">
  <li class="<?php if ($this->list_params['mode'] == 'recent'):?>active<?php endif;?>">
    <a href="javascript:void(0);" rev="recent" class="item <?php if ($this->list_params['mode'] == 'recent'):?>active<?php endif;?> wall_blurlink">
<?php echo $this->translate('WALL_RECENT'  )?>
    </a>
  </li>
    <?php if (count($this->types)):
      $i = 1;

      ?>

      <?php foreach ($this->types as $type):
        if (in_array($type, $list_disabled)){ continue ; }
        if($i>=4){ break; }
        ?>
        <li>
          <a id="link_typs" href="javascript:void(0);" rev="type-<?php echo $type?>" class="item <?php if ($this->list_params['mode'] == 'type' && $type == $this->list_params['type']):?>is_active<?php endif;?> wall_blurlink">

            <?php echo $this->translate('WALL_TYPE_' . strtoupper($type) )?>
          </a>
        </li>
      <?php
    $i++;
    endforeach ;?>


    <?php endif;?>

    <li id = "pinfeed_menu_more"><a href="javascript:void(0);" class="wall-list-button  wall_blurlink"><span class="wall_text" id="pinfeed_more">More<i class="icon-chevron-down"></i> </span></a></li>
</ul>
</div>
</div>

