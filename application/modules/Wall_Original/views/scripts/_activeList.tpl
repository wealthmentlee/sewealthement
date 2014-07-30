<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _activeList.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<a href="javascript:void(0);" class="wall-list-button wall-button wall_blurlink">

  <?php if ($this->list_params['mode'] == 'recent'){ ?>
    <span class="wall_icon wall-most-recent">&nbsp;</span>
    <span class="wall_text"><?php echo $this->translate('WALL_RECENT');?></span>
  <?php } else if ($this->list_params['mode'] == 'type'){?>
    <span class="wall_icon wall-type-<?php echo $this->list_params['type']?>">&nbsp;</span>
    <span class="wall_text"><?php echo $this->translate('WALL_TYPE_' . strtoupper($this->list_params['type']))?></span>
  <?php } else if ($this->list_params['mode'] == 'list'){ ?>
    <span class="wall_icon wall-type-list">&nbsp;</span>
    <span class="wall_text">
    <?php
      foreach ($this->lists as $list){
        if ($list->list_id != $this->list_params['list_id']){
          continue ;
        }
        echo $list->label;
      }
    ?>
    </span>
  <?php } ?>

</a>
