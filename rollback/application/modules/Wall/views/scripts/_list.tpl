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

?>

<li>
  <a href="javascript:void(0);" rev="recent" class="item <?php if ($this->list_params['mode'] == 'recent'):?>is_active<?php endif;?> wall_blurlink">
    <span class="wall_icon_active">&nbsp;</span>
    <span class="wall_icon wall-most-recent">&nbsp;</span>
    <span class="wall_text"><?php echo $this->translate('WALL_RECENT')?></span>
  </a>
</li>


<?php if (count($this->types)):?>

  <?php foreach ($this->types as $type):

    if (in_array($type, $list_disabled)){
      continue ;
    }

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


<?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.frendlistenable', true)):?>

<?php if (count($this->friendlists)):?>

<?php foreach ($this->friendlists as $list):?>
  <li>
    <a href="javascript:void(0);" rev="friendlist-<?php echo $list->list_id?>" class="item <?php if ($this->list_params['mode'] == 'friendlist' && $list->list_id == $this->list_params['list_id']):?>is_active<?php endif;?> wall_blurlink">
      <span class="wall_icon_active">&nbsp;</span>
      <span class="wall_icon wall-type-list">&nbsp;</span>
      <span class="wall_text"><?php echo $list->title?></span>
    </a>
  </li>
  <?php endforeach ;?>


<?php endif;?>

<?php endif;?>


<?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.listenable', true)):?>

  <?php if (count($this->lists)):?>

    <?php foreach ($this->lists as $list):?>
      <li>
        <div class="options">
          <a href="javascript:void(0);" class="edit" title="<?php echo $this->translate('Edit')?>" rev="list_<?php echo $list->list_id?>"></a>
          <a href="javascript:void(0);" class="remove" title="<?php echo $this->translate('Delete')?>" rev="list_<?php echo $list->list_id?>"></a>
        </div>
        <a href="javascript:void(0);" rev="list-<?php echo $list->list_id?>" class="item <?php if ($this->list_params['mode'] == 'list' && $list->list_id == $this->list_params['list_id']):?>is_active<?php endif;?> wall_blurlink">
          <span class="wall_icon_active">&nbsp;</span>
          <span class="wall_icon wall-type-list">&nbsp;</span>
          <span class="wall_text"><?php echo $list->label?></span>
        </a>
      </li>
    <?php endforeach ;?>


  <?php endif;?>

<?php endif;?>

<?php if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('wall.content.listenable', true)):?>

  <li>
    <a href="javascript:void(0);" rev="create-new" class="item wall_blurlink">
      <span class="wall_icon_active">&nbsp;</span>
      <span class="wall_icon wall-list-new">&nbsp;</span>
      <span class="wall_text"><?php echo $this->translate('WALL_TYPE_CREATE_NEW')?></span>
    </a>
  </li>

<?php endif;?>
