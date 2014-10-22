<?php
/**
 * SocialEngine
 *
 * @category   Application_Apptouch
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-12-13 15:13 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Apptouch
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
?>

<?php echo $this->content()->renderWidget('apptouch.admin-main-menu', array('active' => 'apptouch_admin_main_themes')) ?>
<h3 class="sep"><?php echo $this->translate("APPTOUCH_Theme_Editor") ?></h3>

<?php if($this->isTabletEnabled) : ?>
<div class="apptablet_admin_theme_buttons">
    <a class="button active" href="<?php echo $this->url(array('module'=>'apptouch','controller'=>'themes','action'=>'index'), 'admin_default')?>"><?php echo $this->translate('Touch Theme Editor');?></a>
    <a class="button" href="<?php echo $this->url(array('module'=>'apptablet','controller'=>'themes','action'=>'index'), 'admin_default')?>"><?php echo $this->translate('Tablet Theme Editor');?></a>
</div>
<?php endif; ?>

<script type="text/javascript">
  var modifications = [];

  window.onbeforeunload = function() {
    if( modifications.length > 0 ) {
      return '<?php echo $this->translate("If you leave the page now, your changes will be lost. Are you sure you want to continue?") ?>';
    }
  }

  var pushModification = function(type) {
    modifications.push(type);
  }

  var removeModification = function(type) {
    modifications.erase(type);
  }

  var saveFileChanges = function() {
    var request = new Request.JSON({
      url : '<?php echo $this->url(array('action' => 'save')) ?>',
      data : {
        'theme_id' : $('theme_id').value,
        'body' : $('body').value,
        'format' : 'json'
      },
      onComplete : function(responseJSON) {
        if( responseJSON.status ) {
          removeModification('body');
          $$('.admin_themes_header_revert').setStyle('display', 'inline');
          alert('<?php echo $this->string()->escapeJavascript($this->translate("Your changes have been saved!")) ?>');
        } else {
          alert('<?php echo $this->string()->escapeJavascript($this->translate("An error has occurred. Changes could NOT be saved.")) ?>');
        }
      }
    });
    request.send();
  }

  var revertThemeFile = function() {
    var answer = confirm('<?php echo $this->string()->escapeJavascript($this->translate("CORE_VIEWS_SCRIPTS_ADMINTHEMES_INDEX_REVERTTHEMEFILE")) ?>');
    if( !answer ) {
      return;
    }

    var request = new Request.JSON({
      url : '<?php echo $this->url(array('action' => 'revert')) ?>',
      data : {
        'theme_id' : '<?php echo $this->activeTheme->theme_id ?>',
        'format' : 'json'
      },
      onComplete : function() {
        removeModification('body');
        window.location.reload();
//        window.location.replace( window.location.href );
      }
    });
    request.send();
  }
</script>

<div class="admin_theme_editor_wrapper">
  <form action="<?php echo $this->url(array('action' => 'save')) ?>" method="post">
    <div class="admin_theme_edit">

      <div class="admin_theme_header_controls">
        <h3>
          <?php echo $this->translate('Active Theme') ?>
        </h3>
        <div>
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Revert'), array(
          'class' => 'buttonlink admin_themes_header_revert',
          'onclick' => 'revertThemeFile();',
          'style' => !empty($this->modified[$this->activeTheme->name]) ? '':'display:none;')) ?>
<!--        --><?php //echo $this->htmlLink(array('route'=>'admin_default', 'controller'=>'themes', 'action'=>'export','name'=>$this->activeTheme->name),
//          $this->translate('Export'), array(
//            'class' => 'buttonlink admin_themes_header_export',
//          )) ?>
          <?php echo $this->htmlLink(array('route'=>'admin_default', 'module'=>'apptouch', 'controller'=>'themes', 'action'=>'clone', 'name'=>$this->activeTheme->name),
            $this->translate('Clone'), array(
            'class' => 'buttonlink admin_themes_header_clone',
            )) ?>
        <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Save Changes'), array(
          'onclick' => 'saveFileChanges();return false;',
          'class' => 'buttonlink admin_themes_header_save',
        )) ?>
        </div>
      </div>

      <?php if( $this->writeable[$this->activeTheme->name] ): ?>
        <div class="admin_theme_editor_edit_wrapper">

          <div class="admin_theme_editor_selected">
            <?php foreach( $this->themes as $theme ):?>
            <?php
              if ($theme->name === $this->activeTheme->name): ?>
                <div class="theme_selected_info">
                  <h3><?php echo $theme->title?></h3>
                </div>
              <?php break; endif; ?>
            <?php endforeach; ?>
          </div>

          <div class="admin_theme_editor">
            <?php echo $this->formTextarea('body', $this->fileContents, array('onkeypress' => 'pushModification("body")', 'spellcheck' => 'false')) ?>
          </div>
          <button class="activate_button" onclick="saveFileChanges();return false;"><?php echo $this->translate("Save Changes") ?></button>

          <?php echo $this->formHidden('theme_id', $this->activeTheme->theme_id, array()) ?>

        </div>
      <?php else: ?>
        <div class="admin_theme_editor_edit_wrapper">
          <div class="tip">
            <span>

            <?php echo $this->translate('APPTOUCH_VIEWS_SCRIPTS_ADMINTHEMES_INDEX_STYLESHEETSPERMISSION', $this->activeTheme->name) ?>

            </span>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </form>


    <div class="admin_theme_chooser">

        <div class="admin_theme_header_controls">
            <h3>
              <?php echo $this->translate("Available Themes") ?>
            </h3>
        </div>


        <div class="admin_theme_editor_chooser_wrapper">
            <ul class="admin_themes">
              <?php
              $alt_row = true;
              foreach( $this->themes as $theme ):
                $thumb = $this->layout()->staticBaseUrl . 'application/modules/Core/externals/images/anonymous.png';
                if( !empty($this->manifest[$theme->name]['package']['thumb']) )
                  $thumb = $this->manifest[$theme->name]['package']['thumb'];
                ?>
                  <li <?php echo ($alt_row) ? ' class="alt_row"' : "";?>>
                      <div class="theme_wrapper"><img src="<?php echo $thumb ?>" alt="<?php echo $theme->name?>"></div>
                      <div class="theme_chooser_info">
                        <h3><?php echo $theme->title?></h3>
                        <?php if ($theme->name !== $this->activeTheme->name):?>
                          <form action="<?php echo $this->url(array('action' => 'change')) ?>" method="post">
                              <button class="activate_button"><?php echo $this->translate('Activate Theme') ?></button>
                            <?php echo $this->formHidden('theme', $theme->name, array('id'=>'')) ?>
                          </form>
                        <?php else:?>
                          <div class="current_theme">
                              (<?php echo $this->translate("this is your current theme") ?>)
                          </div>
                        <?php endif;?>
                      </div>
                  </li>
                <?php $alt_row = !$alt_row; ?>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>

</div>
