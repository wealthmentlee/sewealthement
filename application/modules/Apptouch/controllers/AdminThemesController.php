<?php
/**
 * SocialEngine
 *
 * @category   Application_Apptouch
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminThemesController.php 2012-12-13 15:13 ulan t $
 * @author     Ulan T
 */

/**
 * @category   Application_Apptouch
 * @package    Apptouch
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Apptouch_AdminThemesController extends Core_Controller_Action_Admin
{
  public function init()
  {

  }

  public function indexAction()
  {
    // Get themes
    $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'apptouch')->fetchAll();
    $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);

    // Install any themes that are missing from the database table
    $reload_themes = false;
    foreach (glob(APPLICATION_PATH . '/application/modules/Apptouch/externals/themes/*', GLOB_ONLYDIR) as $dir) {
      if (file_exists("$dir/manifest.php") && is_readable("$dir/manifest.php") && file_exists("$dir/theme.css") && is_readable("$dir/theme.css")) {
        $name = basename($dir);
        if (!$themes->getRowMatching('name', $name)) {
          $meta = include("$dir/manifest.php");
          $row  = $themes->createRow();
          if( isset($meta['package']['meta']) ) {
            $meta['package'] = array_merge($meta['package']['meta'], $meta['package']);
            unset($meta['package']['meta']);
          }

          $row->title = $meta['package']['title'];
          $row->name  = $name;
          $row->description = isset($meta['package']['description']) ? $meta['package']['description'] : '';
          $row->active = 0;
          $row->save();
          $reload_themes = true;
        }
      }
    }

    foreach ($themes as $theme) {
      if (!is_dir(APPLICATION_PATH . '/application/modules/Apptouch/externals/themes/' . $theme->name)) {
        $theme->delete();
        $reload_themes = true;
      }
    }
    if ($reload_themes) {
      $themes = $this->view->themes = Engine_Api::_()->getDbtable('themes', 'apptouch')->fetchAll();
      $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
      if (empty($activeTheme)) {
        $themes->getRow(0)->active = 1;
        $themes->getRow(0)->save();
        $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
      }
    }

    // Process each theme
    $manifests = array();
    $writeable = array();
    $modified  = array();
    foreach( $themes as $theme ) {
      // Get theme manifest
      $themePath = "application/modules/Apptouch/externals/themes/{$theme->name}";
      $manifest  = @include APPLICATION_PATH . "/$themePath/manifest.php";
      if( !is_array($manifest) )
        $manifest = array(
          'package' => array(),
        );

      // Pre-check manifest thumb
      // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
      if( isset($manifest['package']['meta']) ) {
        $manifest['package'] = array_merge($manifest['package']['meta'], $manifest['package']);
        unset($manifest['package']['meta']);
      }

      if( !isset($manifest['package']['thumb']) ) {
        $manifest['package']['thumb'] = 'thumb.jpg';
      }
      $thumb = preg_replace('/[^A-Z_a-z-0-9\/\.]/', '', $manifest['package']['thumb']);
      if( file_exists(APPLICATION_PATH . "/$themePath/$thumb") ) {
        $manifest['package']['thumb'] = "$themePath/{$thumb}";
      } else {
        $manifest['package']['thumb'] = null;
      }

      // Check if theme files are writeable
      $writeable[$theme->name] = false;
      try {
        if( !file_exists(APPLICATION_PATH . "/$themePath/theme.css") ) {
          throw new Core_Model_Exception('Missing file in theme ' . $manifest['package']['title']);
        } else {
          $this->checkWriteable(APPLICATION_PATH . "/$themePath/theme.css");
        }
        $writeable[$theme->name] = true;
      } catch( Exception $e ) {
        if( $activeTheme->name == $theme->name ) {
          $this->view->errorMessage = $e->getMessage();
        }
      }

      // Check if theme files have been modified
      $modified[$theme->name] = array();
      $originalName = 'original.theme.css';
      if( file_exists(APPLICATION_PATH . "/$themePath/$originalName") ) {
        if( file_get_contents(APPLICATION_PATH . "/$themePath/$originalName") != file_get_contents(APPLICATION_PATH . "/$themePath/theme.css") ) {
          $modified[$theme->name][] = 'theme.css';
        }
      }
      $manifests[$theme->name] = $manifest;
    }

    $this->view->manifest  = $manifests;
    $this->view->writeable = $writeable;
    $this->view->modified  = $modified;

    // Get the first active file
    $this->view->fileContents = file_get_contents(APPLICATION_PATH . '/application/modules/Apptouch/externals/themes/'.$activeTheme->name.'/theme.css');

    $this->view->isTabletEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('apptablet');
  }

  public function saveAction()
  {
    $theme_id = $this->_getParam('theme_id');
    $body = $this->_getParam('body');

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad method");
      return;
    }

    if( !$theme_id || !$body ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad params");
      return;
    }

    // Get theme
    $themeName = $this->_getParam('theme');
    $themeTable = Engine_Api::_()->getDbtable('themes', 'apptouch');
    $themeSelect = $themeTable->select()
      ->orWhere('theme_id = ?', $theme_id)
      ->orWhere('name = ?', $theme_id)
      ->limit(1)
    ;
    $theme = $themeTable->fetchRow($themeSelect);

    if( !$theme ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Missing theme");
      return;
    }

    // Check file
    $basePath     = APPLICATION_PATH . '/application/modules/Apptouch/externals/themes/' . $theme->name;

    $fullFilePath = $basePath . '/theme.css';
    try {
      $this->checkWriteable($fullFilePath);
    } catch( Exception $e ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Not writeable");
      return;
    }

    // Check for original file (try to create if not exists)
    if( !file_exists($basePath . '/original.theme.css') ) {
      if( !copy($fullFilePath, $basePath . '/original.theme.css') ) {
        $this->view->status = false;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_("Could not create backup");
        return;
      }
      chmod("$basePath/original.theme.css", 0777);
    }

    // Now lets write the custom file
    if( !file_put_contents($fullFilePath, $body) ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_('Could not save contents');
      return;
    }

    // clear scaffold cache
    Core_Model_DbTable_Themes::clearScaffoldCache();

    // Increment site counter
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->core_site_counter = $settings->core_site_counter + 1;

    $this->view->status = true;
  }

  public function cloneAction()
  {
    $themes = Engine_Api::_()->getDbtable('themes','apptouch')->fetchAll();
    $form   = $this->view->form = new Core_Form_Admin_Themes_Clone();
    $theme_array = array();
    foreach ($themes as $theme) {
      $theme_array[ $theme->name ] = $theme->title;
    }
    $form->getElement('name')->setMultiOptions($theme_array)->setValue($this->_getParam('name'));

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
      $orig_theme = $this->_getParam('name');
      if (!($row  = $themes->getRowMatching('name', $orig_theme))) {
        throw new Engine_Exception("Theme not found: ".$this->_getParam('name'));
      }
      $new_theme = array(
        'name'        => preg_replace('/[^a-z-0-9_]/', '', strtolower($this->_getParam('title'))),
        'title'       => $this->_getParam('title'),
        'description' => $this->_getParam('description'),
      );
      self::cloneThemeFiles($orig_theme, $new_theme['name']);
      $orig_dir = APPLICATION_PATH . '/application/modules/Apptouch/externals/themes/'.$orig_theme;
      $new_dir  = dirname($orig_dir) . '/' . $new_theme['name'];

      $meta = include("$new_dir/manifest.php");
      // @todo meta key is deprecated and pending removal in 4.1.0; b/c removal in 4.2.0
      if( isset($meta['package']['meta']) ) {
        $meta['package'] = array_merge($meta['package']['meta'], $meta['package']);
        unset($meta['package']['meta']);
      }
      $meta['package']['name']        = $new_theme['name'];
      $meta['package']['version']     = null;
      $meta['package']['path']        = substr($new_dir, 1 + strlen(APPLICATION_PATH));
      $meta['package']['title']       = $new_theme['title'];
      $meta['package']['description'] = $new_theme['description'];
      $meta['package']['author']      = $this->_getParam('author', '');
      file_put_contents("$new_dir/manifest.php",  '<?php return ' . var_export($meta, true) . '; ?>');

      try {
        Engine_Api::_()->getDbtable('themes', 'apptouch')->createRow(array(
          'name'  => $new_theme['name'],
          'title' => $new_theme['title'],
          'description' => $new_theme['description'],
        ));
      } catch (Exception $e) { /* do nothing */ }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  public function revertAction()
  {
    $theme_id = $this->_getParam('theme_id');

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad method");
      return;
    }

    if( !$theme_id ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad params");
      return;
    }

    // Get theme
    $themeName = $this->_getParam('theme');
    $themeTable = Engine_Api::_()->getDbtable('themes', 'apptouch');
    $themeSelect = $themeTable->select()
      ->orWhere('theme_id = ?', $theme_id)
      ->orWhere('name = ?', $theme_id)
      ->limit(1)
    ;
    $theme = $themeTable->fetchRow($themeSelect);

    // Check file
    $basePath = APPLICATION_PATH . '/application/modules/Apptouch/externals/themes/' . $theme->name;
    if( file_exists("$basePath/original.theme.css") ) {
      // Check each file if writeable
      $this->checkWriteable($basePath . '/');
      $this->checkWriteable($basePath . '/theme.css');
      $this->checkWriteable($basePath . '/original.theme.css');

      // Now undo all of the changes
      unlink("$basePath/theme.css");
      rename("$basePath/original.theme.css", "$basePath/theme.css");
    }

    // clear scaffold cache
    Core_Model_DbTable_Themes::clearScaffoldCache();

    // Increment site counter
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->core_site_counter = $settings->core_site_counter + 1;

//    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function checkWriteable($path)
  {
    if( !file_exists($path) ) {
      throw new Core_Model_Exception('Path doesn\'t exist');
    }
    if( !is_writeable($path) ) {
      throw new Core_Model_Exception('Path is not writeable');
    }
    if( !is_dir($path) ) {
      if( !($fh = fopen($path, 'ab')) ) {
        throw new Core_Model_Exception('File could not be opened');
      }
      fclose($fh);
    }
  }

  public function changeAction()
  {
    $themeName = $this->_getParam('theme');
    $themeTable = Engine_Api::_()->getDbtable('themes', 'apptouch');
    $themeSelect = $themeTable->select()
      ->orWhere('theme_id = ?', $themeName)
      ->orWhere('name = ?', $themeName)
      ->limit(1)
    ;
    $theme = $themeTable->fetchRow($themeSelect);

    if( $theme && $this->getRequest()->isPost() ) {
      $db = $themeTable->getAdapter();
      $db->beginTransaction();

      try {
        $themeTable->update(array(
          'active' => 0,
        ), array(
          '1 = ?' => 1,
        ));
        $theme->active = true;
        $theme->save();

        // clear scaffold cache
        Core_Model_DbTable_Themes::clearScaffoldCache();

        // Increment site counter
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $settings->core_site_counter = $settings->core_site_counter + 1;

        $db->commit();

      } catch( Exception $e ) {
        $db->rollBack();
        throw $e;
      }
    }

    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  /**
   * outputs all files and directories
   * recursively starting with the given
   * $base path. This function is a combination
   * of some of the other snips on the php.net site.
   *
   * @example rscandir(dirname(__FILE__).'/'));
   * @param string $base
   * @param array $data
   * @return array
   */
  public static function rscandir($base='', &$data=array()) {
      $array = array_diff(scandir($base), array('.', '..')); // remove ' and .. from the array */
      foreach($array as $value) { /* loop through the array at the level of the supplied $base */
        if ( is_dir("$base/$value") ) { /* if this is a directory */
          $data[] = "$base/$value/"; /* add it to the $data array */
          $data   = self::rscandir("$base/$value", $data); /* then make a recursive call with the
          current $value as the $base supplying the $data array to carry into the recursion */
        } elseif (is_file("$base/$value")) { /* else if the current $value is a file */
          $data[] = "$base/$value"; /* just add the current $value to the $data array */
        }
      }
      return $data; // return the $data array
  }
  public static function cloneThemeFiles($orig_theme, $new_theme){
    $orig_dir = APPLICATION_PATH . '/application/modules/Apptouch/externals/themes/'.$orig_theme;
    $new_dir  = dirname($orig_dir) . '/' . $new_theme;
    Engine_Package_Utilities::fsCopyRecursive($orig_dir, $new_dir);
    chmod($new_dir, 0777);
    foreach (self::rscandir($new_dir) as $file)
      chmod($file, 0777);
    if (Engine_Api::_()->hasModuleBootstrap('apptablet')){
      $orig_dir = APPLICATION_PATH . '/application/modules/Apptablet/externals/themes/'.$orig_theme;
      $new_dir  = dirname($orig_dir) . '/' . $new_theme;
      Engine_Package_Utilities::fsCopyRecursive($orig_dir, $new_dir);
      chmod($new_dir, 0777);
      foreach (self::rscandir($new_dir) as $file)
        chmod($file, 0777);
    }
  }
}