<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _list.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */


$list_disabled = explode(',', Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.list.disabled', ''));
$i=0;
?>
<?php if (count($this->types)):?>

  <?php foreach ($this->types as $type):

    if (in_array($type, $list_disabled)){
      continue ;
    }
    $i++;
    if( $i <= 3 ) continue;
    ?>
    <li>
      <a href="javascript:void(0);" rev="type-<?php echo $type?>" class="item <?php if ($this->list_params['mode'] == 'type' && $type == $this->list_params['type']):?>is_active<?php endif;?> wall_blurlink">
        <span class="wall_icon_active">&nbsp;</span>
        <span class="wall_icon wall-type-<?php echo $type?>">&nbsp;</span>
        <span class="wall_text"><?php echo $this->translate('WALL_TYPE_' . strtoupper($type) )?></span>
      </a>
    </li>
  <?php endforeach ;?>


<?php endif;?>
