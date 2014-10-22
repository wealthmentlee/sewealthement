<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-04-26 11:18:13 mirlan $
 * @author     Mirlan
 */

?>
<script type="text/javascript">

  var SortablesInstance;

  window.addEvent('domready', function () {
    $$('.item_label').addEvents({
      mouseover:showPreview,
      mouseout:showPreview
    });
  });

  var showPreview = function (event) {
    try {
      element = $(event.target);
      element = element.getParent('.admin_menus_item').getElement('.item_url');
      if (event.type == 'mouseover') {
        element.setStyle('display', 'block');
      } else if (event.type == 'mouseout') {
        element.setStyle('display', 'none');
      }
    } catch (e) {
    }
  }


  window.addEvent('load', function () {
    SortablesInstance = new Sortables('menu_list', {
      clone:true,
      constrain:false,
      handle:'.item_label',
      onComplete:function (e) {
        reorder(e);
      }
    });
    $$('.switcher').addEvent('click', function(e){
      var menuitem = this.getParent().getParent();
      if(menuitem.hasClass('disabled'))
        menuitem.removeClass('disabled');
      else
        menuitem.addClass('disabled');
      var request = new Request.JSON({
        'url':this.get('href'),
        'method':'POST',
        'data': {'format': 'json'},
        onSuccess:function (responseJSON) {
          if(responseJSON.enabled)
            menuitem.removeClass('disabled');
          else
            menuitem.addClass('disabled');
        }
      });

      request.send();
    });

  });

  var reorder = function (e) {
    var menuitems = e.parentNode.childNodes;
    var ordering = {};
    var i = 1;
    for (var menuitem in menuitems) {
      var child_id = menuitems[menuitem].id;

      if ((child_id != undefined) && (child_id.substr(0, 5) == 'admin')) {
        ordering[child_id] = i;
        i++;
      }
    }
    ordering['menu'] = '<?php echo $this->selectedMenu->name;?>';
    ordering['format'] = 'json';

    // Send request
    var url = '<?php echo $this->url(array('action' => 'order')) ?>';
    var request = new Request.JSON({
      'url':url,
      'method':'POST',
      'data':ordering,
      onSuccess:function (responseJSON) {
      }
    });

    request.send();
  }

  function ignoreDrag() {
    event.stopPropagation();
    return false;
  }
</script>
<?php echo $this->content()->renderWidget('apptouch.admin-main-menu', array('active' => 'apptouch_admin_main_content')) ?>
<h3>
  <?php echo $this->translate('Menu Editor') ?>
</h3>
<p>
  <?php echo $this->translate('CORE_VIEWS_SCRIPTS_ADMINMENU_INDEX_DESCRIPTION') ?>
</p>

<br/>

<div class="admin_menus_filter">
  <form action="<?php echo $this->url() ?>" method="get">
    <h3><?php echo $this->translate("Editing:") ?></h3>
    <?php echo $this->formSelect('name', $this->selectedMenu->name, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->menuList) ?>
  </form>
</div>

<br/>

<div class="admin_menus_options">
  <?php echo $this->htmlLink(array('reset' => false, 'action' => 'create', 'name' => $this->selectedMenu->name), $this->translate('Add Item'), array('class' => 'buttonlink admin_menus_additem smoothbox')) ?>
</div>

<br/>

<ul class="admin_menus_items" id='menu_list'>
  <?php foreach ($this->menuItems as $menuItem):?>
  <?php $data = $menuItem->data_attrs ? Zend_Json_Decoder::decode($menuItem->data_attrs) : array();?>
  <?php
    $class = 'item_wrapper';
    if( !empty($data['role']) && $data['role'] == 'list-divider' ) {
      $class = 'item_divider';
    }
  ?>
  <li class="admin_menus_item<?php if (isset($menuItem->enabled) && !$menuItem->enabled) echo ' disabled' ?>"
      id="admin_menus_item_<?php echo $menuItem->name ?>">
      <span class="<?php echo $class;?>">
        <span class="item_options">
          <?php echo $this->htmlLink(array('reset' => false, 'action' => 'edit', 'name' => $menuItem->name), $this->translate('edit'), array('class' => 'smoothbox')) ?>
          <?php if ($menuItem->custom): ?>
          | <?php echo $this->htmlLink(array('reset' => false, 'action' => 'delete', 'name' => $menuItem->name), $this->translate('delete'), array('class' => 'smoothbox')) ?>
          <?php endif; ?>
        </span>
        <span class="switcher" href="<?php echo $this->url(array('action' => 'on-off', 'name' => $menuItem->name)) ?>"><span class="on"><?php echo $this->translate('AT_on') ?></span><span class="off"><?php echo $this->translate('AT_off') ?></span></span>
        <span class="item_label">
          <?php echo $this->translate($menuItem->label) ?>
        </span>
        <span class="item_url">
          <?php
          $href = '';
          if(!empty($data['role']) && $data['role'] == 'list-divider'){
            echo $this->translate('APPTOUCH_Divider');
          } else if (isset($menuItem->params['uri'])) {
            echo $this->htmlLink($menuItem->params['uri'], $menuItem->params['uri']);
          } else if (!empty($menuItem->plugin)) {
            echo '<a>(' . $this->translate('variable') . ')</a>';
          } else {
            echo $this->htmlLink($this->htmlLink()->url($menuItem->params), $this->htmlLink()->url($menuItem->params));
          }
          ?>
        </span>
      </span>
  </li>
  <?php endforeach; ?>
</ul>
