<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */


$this->headScript()
      ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/core.js');

$this->headTranslate(array(
  'WALL_LIST_EMPTY',
  'WALL_LIST_EMPTY_SEARCH',
  'WALL_LIST_SELECTED_EMPTY'
));

?>


<script type="text/javascript">

  window.addEvent('load', function(){
    var list = new Wall.List({
      'url': {
        'browse': '<?php echo $this->url(array(), 'wall_list', true)?>',
        'save': '<?php echo $this->url(array('action' => 'save'), 'wall_list', true)?>'
      }
      <?php if ($this->edit):?>,
      selected: <?php echo $this->jsonInline($this->guids)?>,
      is_edit: true,
      list_id: <?php echo $this->list->list_id;?>
      <?php endif;?>
    });
  });
  
</script>


<div class="wall-list" id="wall-list">

  <?php if ($this->edit):?>

    <div class="header-title">
      <h3><?php echo $this->translate('WALL_LIST_EDIT_HEADER_TITLE')?></h3>
    </div>
    <div class="header-description">
      <?php echo $this->translate('WALL_LIST_EDIT_HEADER_DESCRIPTION')?>
    </div>

  <?php else :?>

    <div class="header-title">
      <h3><?php echo $this->translate('WALL_LIST_CREATE_HEADER_TITLE')?></h3>
    </div>
    <div class="header-description">
      <?php echo $this->translate('WALL_LIST_CREATE_HEADER_DESCRIPTION')?>
    </div>

  <?php endif;?>


  <form action="" class="form">

    <ul class="form-container">
      <li>
        <span><?php echo $this->translate('WALL_LIST_LABEL')?></span>
        <span>
          <input type="text" name="label" value="<?php if ($this->edit):?><?php echo $this->list->label?><?php endif;?>"/>
        </span>
      </li>
    </ul>

  </form>

  <div class="tabs-container">
    <ul>
      <li><a href="javascript:void(0);" class="all is_active"><?php echo $this->translate('WALL_LIST_ALL')?></a></li>
      <?php foreach ($this->tabs as $tab):?>
        <li><a href="javascript:void(0);" class="type" rev="type-<?php echo $tab?>"><?php echo $this->translate('WALL_LIST_' . strtoupper($tab))?></a></li>
      <?php endforeach;?>
      <li><a href="javascript:void(0);" class="selected"><?php echo $this->translate('WALL_LIST_SELECTED')?> (<span class="selected_count">0</span>)</a></li>
    </ul>

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

    <div class="prev all-prev <?php if (empty($this->prev)):?>disabled<?php endif;?>"><a href="javascript:void(0);"></a></div>
    <div class="prev select-prev" style="display: none;"><a href="javascript:void(0);"></a></div>

    <div class="items-container">
      <div class="container">
        <div class="all-container is_active">
          <ul class="items">
            <?php echo $this->render('list/pagination.tpl')?>
          </ul>
        </div>
        <div class="selected-container">

          <ul class="items">
            <?php echo $this->partial('list/_items.tpl', null, array('items' => $this->selected))?>
            <li class="message" style="display: none;">
              <div>
                <span><?php echo $this->translate('WALL_LIST_SELECTED_EMPTY')?></span>
              </div>
            </li>
          </ul>

        </div>
      </div>
    </div>

    <div class="next all-next <?php if (empty($this->next)):?>disabled<?php endif;?>"><a href="javascript:void(0);"></a></div>
    <div class="next select-next" style="display: none;"><a href="javascript:void(0);"></a></div>

  </div>

  <div class="submit-container">
    <button type="submit" class="form-submit"><?php echo $this->translate('Save Changes')?></button>
    &nbsp;<?php echo $this->translate('or')?>&nbsp;
    <a href="javascript:void(0);" onClick="parent.Smoothbox.close();"><?php echo $this->translate('cancel')?>
  </div>

</div>