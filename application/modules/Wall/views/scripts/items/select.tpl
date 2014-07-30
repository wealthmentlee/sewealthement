<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: select.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>
<?php

$this->headScript()
      ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/core.js');

$this->headTranslate(array(
  'WALL_ITEMS_EMPTY',
  'WALL_ITEMS_EMPTY_SEARCH',
));

?>

<script type="text/javascript">
  Wall.runonce.add(function (){
    new Wall.Select('wall-items', <?php echo $this->jsonInline(array('params' => array_merge($this->params, array('m' => $this->m, 'fn' => $this->fn, 'selected' => $this->selected))))?>);
  });
</script>


<div class="wall-items" id="wall-items">


  <div class="total-header">

    <div class="header">
      <div class="header-title">
        <?php echo $this->translate('WALL_ITEMS_SELECT_'.strtoupper($this->fn).'_HEADER_TITLE')?>
      </div>
      <div class="header-description">
        <?php echo $this->translate('WALL_ITEMS_SELECT_'.strtoupper($this->fn).'_HEADER_DESCRIPTION')?>
      </div>
    </div>

    <div class="search-form">
      <form action="">
        <div class="search">
          <input type="text" name="search" value="" />
          <a href="javascript:void(0);" class="search-submit"></a>
        </div>
      </form>
    </div>

  </div>

  
  <div class="elements">

    <div class="prev <?php if (!$this->prev):?>disabled<?php endif;?>"><a href="javascript:void(0);"></a></div>

    <div class="items-container">
      <div class="container">
        <ul class="items">
          <?php echo $this->render('items/_items.tpl')?>
        </ul>
      </div>
    </div>

    <div class="next <?php if (!$this->next):?>disabled<?php endif;?>"><a href="javascript:void(0);"></a></div>
  </div>

  <div class="submit-container">
    <button type="button" class="form-submit" onClick="parent.Smoothbox.close();"><?php echo $this->translate('Cancel')?></button>
  </div>

</div>