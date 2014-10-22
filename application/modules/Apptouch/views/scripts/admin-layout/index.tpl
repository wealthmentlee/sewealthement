<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-12-03 11:18:13 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Extensions
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>

<script type="text/javascript">
    window.addEvent('load', function () {
        SortablesInstance = new Sortables('menu_list', {
            clone:true,
            constrain:false,
            handle:'.item_label',
            onComplete:function (e) {
                reorder(e);
            }
        });
    });
</script>

<?php echo $this->content()->renderWidget('apptouch.admin-main-menu', array('active' => 'apptouch_admin_main_layout')) ?>
<h3><?php echo $this->translate("APPTOUCH_Layout_Editor") ?></h3>

<br>
<div class="admin_layoutbox_menu">
    <ul>
        <li class="admin_layoutbox_menu_generic" id="admin_layoutbox_menu_openpage">
            <div class="admin_layoutbox_menu_wrapper_generic admin_layoutbox_menu_pages_wrapper" id="admin_layoutbox_menu_pages_wrapper">
                <ul class="admin_layoutbox_menu_pages">
                  <?php foreach( $this->allPages as $page ): ?>
                        <li onclick="loadPage(<?php echo $page->page_id ?>);">
                          <?php echo $page->displayname ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="javascript:void(0);" onClick="switchPageMenu(new Event(event), $(this));">
                <span><?php echo $this->translate("Editing:"); ?></span>
              <?php echo $this->pageObject->displayname ?><span class="more">&nbsp;</span>
            </a>
        </li>
<!--        <li id="admin_layoutbox_menu_savechanges">-->
<!--            <a href="javascript:void(0);" onClick="saveChanges()">-->
<!--              --><?php //echo $this->translate("Save Changes") ?>
<!--            </a>-->
<!--        </li>-->
    </ul>
</div>


<div class="settings">
  <div class="admin_home_middle">
    <?php echo $this->form->render($this) ?>
  </div>
</div>

<script type="text/javascript">
    /* Switch the active menu item */
    var switchPageMenu = function(event, activator) {
        var element = activator.getParent('li');
        $$('.admin_layoutbox_menu_generic').each(function(otherElement) {
            var otherWrapper = otherElement.getElement('.admin_layoutbox_menu_wrapper_generic');
            if( otherElement.get('id') == element.get('id') && !otherElement.hasClass('active') ) {
                otherElement.addClass('active');
                otherWrapper.setStyle('display', 'block');
                var firstInput = otherElement.getElement('input');
                if( firstInput ) {
                    firstInput.focus();
                }
            } else {
                otherElement.removeClass('active');
                otherWrapper.setStyle('display', 'none');
            }
        });
    }

    /* Load a different page */
    var loadPage = function(page_id) {
      window.location.search = '?page=' + page_id;
    }

    var saveChanges = function()
    {
      var data = {};
      var inputs = $$('#apptouch_layout_form input');
      inputs.each(function(item, index){
      });
    }
</script>