<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminThemesController.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_AdminThemesController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('mobile_admin_main', array(), 'mobile_admin_main_themes');

    // Get themes
    $themes      = $this->view->themes      = Engine_Api::_()->getDbtable('themes', 'mobile')->fetchAll();
    $activeTheme = $this->view->activeTheme = $themes->getRowMatching('active', 1);
    // Install any themes that are missing from the database table
    $reload_themes = false;
    foreach (glob(APPLICATION_PATH . '/application/modules/Mobile/themes/*', GLOB_ONLYDIR) as $dir) {
      if (file_exists("$dir/manifest.php") && is_readable("$dir/manifest.php") && file_exists("$dir/theme.css") && is_readable("$dir/theme.css")) {
        $name = basename($dir);
        if (!$themes->getRowMatching('name', $name)) {
          $meta = include("$dir/manifest.php");
          $row  = $themes->createRow();
          // @todo meta key is deprecated and pending removal in 4.1.0; merge into main array
          $row->title = $meta['package']['meta']['title'];
          $row->name  = $name;
          $row->description = isset($meta['package']['meta']['description']) ? $meta['package']['meta']['description'] : '';
          $row->active = 0;
          $row->save();
          $reload_themes = true;
        }
      }
    }
    foreach ($themes as $theme) {
      if (!is_dir(APPLICATION_PATH . '/application/modules/Mobile/themes/' . $theme->name)) {
        $theme->delete();
        $reload_themes = true;
      }
    }
    if ($reload_themes) {
      $themes        = $this->view->themes      = Engine_Api::_()->getDbtable('themes', 'mobile')->fetchAll();
      $activeTheme   = $this->view->activeTheme = $themes->getRowMatching('active', 1);
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
      $themePath = "application/modules/Mobile/themes/{$theme->name}";
      $manifest  = @include APPLICATION_PATH . "/$themePath/manifest.php";
      if( !is_array($manifest) )
        $manifest = array(
          'package' => array(),
          'files' => array()
      );
      sort($manifest['files']);
      
      // Pre-check manifest thumb
      // @todo meta key is deprecated and pending removal in 4.1.0; merge into main array
      if( !isset($manifest['package']['meta']['thumb']) )
        $manifest['package']['meta']['thumb'] = 'thumb.jpg';
      $thumb = preg_replace('/[^A-Z_a-z-0-9\/\.]/', '', $manifest['package']['meta']['thumb']);
      if( file_exists(APPLICATION_PATH . "/$themePath/$thumb") )
        $manifest['package']['meta']['thumb'] = "$themePath/{$thumb}";
      else
        $manifest['package']['meta']['thumb'] = null;

      // Check if theme files are writeable
      $writeable[$theme->name] = false;
      try {
        foreach( array_merge(array(''), $manifest['files']) as $file ) {
          if( !file_exists(APPLICATION_PATH . "/$themePath/$file") ) {
            throw new Core_Model_Exception('Missing file in theme ' . $manifest['package']['meta']['title']);
          } else {
            $this->checkWriteable(APPLICATION_PATH . "/$themePath/$file");
          }
        }
        $writeable[$theme->name] = true;
      } catch( Exception $e ) {
        if( $activeTheme->name == $theme->name ) {
          $this->view->errorMessage = $e->getMessage();
        }
      }

      // Check if theme files have been modified
      $modified[$theme->name] = array();
      foreach( $manifest['files'] as $path ) {
        $originalName = 'original.' . $path;
        if( file_exists(APPLICATION_PATH . "/$themePath/$originalName"))
          if( file_get_contents(APPLICATION_PATH . "/$themePath/$originalName") != file_get_contents(APPLICATION_PATH . "/$themePath/$path") )
            $modified[$theme->name][] = $path;
      }
      $manifests[$theme->name] = $manifest;
    }
    
    $this->view->manifest  = $manifests;
    $this->view->writeable = $writeable;
    $this->view->modified  = $modified;

    // Get the first active file
    $this->view->activeFileName = $activeFileName = $manifests[$activeTheme->name]['files'][0];
    if( null !== ($rFile = $this->_getParam('file')) ) {
      if( in_array($rFile, $manifests[$activeTheme->name]['files']) ) {
        $this->view->activeFileName = $activeFileName = $rFile;
      }
    }
    $this->view->activeFileOptions = array_combine($manifests[$activeTheme->name]['files'], $manifests[$activeTheme->name]['files']);
    $this->view->activeFileContents = file_get_contents(APPLICATION_PATH . '/application/modules/Mobile/themes/'.$activeTheme->name.'/'.$activeFileName);
  }

  public function changeAction()
  {
    if (!$this->getRequest()->isPost() || _ENGINE_ADMIN_NEUTER) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'mobile', 'controller' => 'themes', 'action' => 'index'), 'admin_default', true);
    }
    $themeName = $this->_getParam('theme');
    $themeTable = Engine_Api::_()->getDbtable('themes', 'mobile');
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

  public function saveAction()
  {
    if (_ENGINE_ADMIN_NEUTER) {
      return $this->_helper->viewRenderer->setNoRender(true);
    }
    $theme_id = $this->_getParam('theme_id');
    $file     = $this->_getParam('file');
    $body     = $this->_getParam('body');

    if( !$this->getRequest()->isPost() ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad method");
      return;
    }

    if( !$theme_id || !$file || !$body ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Bad params");
      return;
    }

    // Get theme
    $themeName = $this->_getParam('theme');
    $themeTable = Engine_Api::_()->getDbtable('themes', 'mobile');
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
    $basePath     = APPLICATION_PATH . '/application/modules/Mobile/themes/' . $theme->name;
    $manifestData = include $basePath . '/manifest.php';
    if( empty($manifestData['files']) || !in_array($file, $manifestData['files']) ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Not in theme files");
      return;
    }
    $fullFilePath = $basePath . '/' . $file;
    try {
      $this->checkWriteable($fullFilePath);
    } catch( Exception $e ) {
      $this->view->status = false;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Not writeable");
      return;
    }

    // Check for original file (try to create if not exists)
    if( !file_exists($basePath . '/original.' . $file) ) {
      if( !copy($fullFilePath, $basePath . '/original.' . $file) ) {
        $this->view->status = false;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_("Could not create backup");
        return;
      }
      chmod("$basePath/original.$file", 0777);
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

  public function revertAction()
  {
    if (_ENGINE_ADMIN_NEUTER) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'mobile', 'controller' => 'themes', 'action' => 'index'), 'admin_default', true);
    }
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
    $themeTable = Engine_Api::_()->getDbtable('themes', 'mobile');
    $themeSelect = $themeTable->select()
      ->orWhere('theme_id = ?', $theme_id)
      ->orWhere('name = ?', $theme_id)
      ->limit(1)
      ;
    $theme = $themeTable->fetchRow($themeSelect);

    // Check file
    $basePath = APPLICATION_PATH . '/application/modules/Mobile/themes/' . $theme->name;
    $manifestData = include $basePath . '/manifest.php';
    $files = $manifestData['files'];
    $originalFiles = array();
    foreach( $files as $file ) {
      if( file_exists("$basePath/original.$file") ) {
        $originalFiles[] = $file;
      }
    }

    // Check each file if writeable
    $this->checkWriteable($basePath . '/');
    foreach( $originalFiles as $file ) {
      //try {
        $this->checkWriteable($basePath . '/' . $file);
        $this->checkWriteable($basePath . '/original.' . $file);
      //} catch( Exception $e ) {
      //  $this->view->status = false;
      //  $this->view->message = 'Not writeable';
      //  return;
      //}
    }

    // Now undo all of the changes
    foreach( $originalFiles as $file ) {
      unlink("$basePath/$file");
      rename("$basePath/original.$file", "$basePath/$file");
    }

    // clear scaffold cache
    Core_Model_DbTable_Themes::clearScaffoldCache();

    // Increment site counter
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $settings->core_site_counter = $settings->core_site_counter + 1;

    return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
  }

  public function exportAction()
  {
    if (_ENGINE_ADMIN_NEUTER) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'mobile', 'controller' => 'themes', 'action' => 'index'), 'admin_default', true);
    }
    $themes = Engine_Api::_()->getDbtable('themes', 'mobile')->fetchAll();
    if (!($row = $themes->getRowMatching('name', $this->_getParam('name')))) {
      throw new Engine_Exception("Theme not found: ".$this->_getParam('name'));
    }
    //$targetFilename = APPLICATION_PATH . '/temporary/theme_export.tar';
    $target_filename = tempnam(APPLICATION_PATH . '/temporary/', 'theme_');
    $template_dir    = APPLICATION_PATH . '/application/modules/Mobile/themes/'.$row->name;
    
    $tar = new Archive_Tar($target_filename);
    $tar->setIgnoreRegexp("#CVS|\.svn#");
    $tar->createModify($template_dir, null, dirname($template_dir));
    chmod($target_filename, 0777);
    header('Content-Type: application/x-tar');
    header("Content-disposition: filename={$row->name}.tar");
    readfile($target_filename);
    @unlink($target_filename);
    exit;
  }

  public function cloneAction()
  {
    if (_ENGINE_ADMIN_NEUTER) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'mobile', 'controller' => 'themes', 'action' => 'index'), 'admin_default', true);
    }
    $themes = Engine_Api::_()->getDbtable('themes', 'mobile')->fetchAll();
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
      $orig_dir = APPLICATION_PATH . '/application/modules/Mobile/themes/'.$orig_theme;
      $new_dir  = dirname($orig_dir) . '/' . $new_theme['name'];

      Engine_Package_Utilities::fsCopyRecursive($orig_dir, $new_dir);
      chmod($new_dir, 0777);
      foreach (self::rscandir($new_dir) as $file)
        chmod($file, 0777);
      
      $meta = include("$new_dir/manifest.php");
      // @todo meta key is deprecated and pending removal in 4.1.0; merge into main array
      $meta['package']['name']           = $new_theme['title'];
      $meta['package']['version']         = null;
      $meta['package']['path']            = substr($new_dir, 1+strlen(APPLICATION_PATH));
      $meta['package']['meta']['title']   = $new_theme['title'];
      $meta['package']['meta']['name']    = $new_theme['name'];
      $meta['package']['meta']['author'] = $this->_getParam('author', '');
      file_put_contents("$new_dir/manifest.php",  '<?php return '.var_export($meta, true).'; ?>');

      try {
        Engine_Api::_()->getDbtable('themes', 'mobile')->createRow(array(
          'name'  => $new_theme['name'],
          'title' => $new_theme['title'],
          'description' => $new_theme['description'],
        ));
      } catch (Exception $e) { /* do nothing */ }

      $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }
  }

  public function uploadAction()
  {
    if (_ENGINE_ADMIN_NEUTER) {
      return $this->_helper->redirector->gotoRoute(array('module' => 'mobile', 'controller' => 'themes', 'action' => 'index'), 'admin_default', true);
    }
    $form = $this->view->form = new Core_Form_Admin_Themes_Upload();
    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {

      if (!isset($_FILES['theme_file']))
        throw new Engine_Exception("Theme file was too large, or was not uploaded.");

      if (!preg_match('/\.tar$/', $_FILES['theme_file']['name']))
        throw new Engine_Exception("Invalid theme file format; must be a TAR file.");

      // extract tar file to temporary directory
      $tmp_dir = tempnam(APPLICATION_PATH . '/temporary/', 'theme_import');
      unlink($tmp_dir);
      mkdir($tmp_dir, 0777, true);
      $tar = new Archive_Tar($_FILES['theme_file']['tmp_name']);
      $tar->extract($tmp_dir);
      
      // find theme.css
      $dir = $tmp_dir;
      while (!file_exists("$dir/theme.css")) {
        $subdirs = glob("$dir/*", GLOB_ONLYDIR);
        $dir     = $subdirs[0];
      }
      
      // pull manifest.php data
      $meta = array('package'=>array(),'files'=>array());
      if (file_exists("$dir/manifest.php")) {
        $meta = include "$dir/manifest.php";
        $post = $this->_getAllParams();
        // @todo meta key is deprecated and pending removal in 4.1.0; merge into main array
        if (isset($post['title'])) {
          $meta['package']['meta']['title'] = $post['title'];
          $meta['package']['name']  = preg_replace('/[^a-z-0-9_]/', '', strtolower($post['title']));
        }
        if (empty($meta['package']['name']))
          $meta['package']['name'] = basename($dir);
        if (empty($meta['package']['meta']['title']))
          $meta['package']['meta']['title'] = ucwords(preg_replace('/_\-/', ' ', basename($dir)));
        if (isset($post['description'])) {
          $meta['package']['meta']['description'] = $post['description'];
        }
      }
      
      // move files over recursively
      $destination_dir = APPLICATION_PATH . '/application/modules/Mobile/themes/'.$meta['package']['name'];
      rename($dir, $destination_dir);
      chmod($destination_dir, 0777);
      foreach (self::rscandir($destination_dir) as $file) {
        chmod($file, 0777);
      }
      
      // re-write manifest according to POST paramters
      file_put_contents("$destination_dir/manifest.php",  '<?php return '.var_export($meta, true).'; ?>');

      // add to database table
      $table = Engine_Api::_()->getDbtable('themes', 'mobile');
      $row   = $table->createRow();
      // @todo meta key is deprecated and pending removal in 4.1.0; merge into main array
      $row->name        = $meta['package']['name'];
      $row->title       = $meta['package']['meta']['title'];
      $row->description = $meta['package']['meta']['description'];
      $row->active      = $this->_getParam('enable', false);
      $row->save();

      // delete temporary directory
      Engine_Package_Utilities::fsRmdirRecursive($tmp_dir, true);

      // clear scaffold cache
      Core_Model_DbTable_Themes::clearScaffoldCache();

      // Increment site counter
      $settings = Engine_Api::_()->getApi('settings', 'core');
      $settings->core_site_counter = $settings->core_site_counter + 1;

      // forward back to index
      $this->_forward('success', 'utility', 'core', array(
        'redirect' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action'=>'index')),
        'messages' => array(Zend_Registry::get('Zend_Translate')->_('Theme file has been uploaded.')),
        'parentRefresh'  => 2000,
      ));
    }
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
}