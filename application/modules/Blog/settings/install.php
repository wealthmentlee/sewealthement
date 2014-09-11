<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: install.php 9895 2013-02-14 00:12:22Z shaun $
 * @author     Steve
 */

/**
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Blog_Installer extends Engine_Package_Installer_Module
{
  public function onInstall()
  {
    $this->_addUserProfileContent();
    $this->_addBlogListPage();
    $this->_addBlogViewPage();
    $this->_addBlogBrowsePage();
    
    $this->_addBlogCreatePage();
    $this->_addBlogManagePage();
    
    parent::onInstall();
  }
  
  protected function _addBlogManagePage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'blog_index_manage')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'blog_index_manage',
        'displayname' => 'Blog Manage Page',
        'title' => 'My Entries',
        'description' => 'This page lists a user\'s blog entries.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert main-right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 1,
      ));
      $main_right_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.browse-menu',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
      
      // Insert search
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.browse-search',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 1,
      ));
      
      // Insert gutter menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.browse-menu-quick',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 2,
      ));
    }
  }

  
  protected function _addBlogCreatePage()
  {
  
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'blog_index_create')
      ->limit(1)
      ->query()
      ->fetchColumn();
      
    if( !$page_id ) {
      
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'blog_index_create',
        'displayname' => 'Blog Create Page',
        'title' => 'Write New Entry',
        'description' => 'This page is the blog create page.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.browse-menu',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
    }
  }
  
  protected function _addBlogBrowsePage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'blog_index_index')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'blog_index_index',
        'displayname' => 'Blog Browse Page',
        'title' => 'Blog Browse',
        'description' => 'This page lists blog entries.',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert top
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'top',
        'page_id' => $page_id,
        'order' => 1,
      ));
      $top_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
        'order' => 2,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert top-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $top_id,
      ));
      $top_middle_id = $db->lastInsertId();
      
      // Insert main-middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $main_middle_id = $db->lastInsertId();
      
      // Insert main-right
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'right',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 1,
      ));
      $main_right_id = $db->lastInsertId();
      
      // Insert menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.browse-menu',
        'page_id' => $page_id,
        'parent_content_id' => $top_middle_id,
        'order' => 1,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $main_middle_id,
        'order' => 1,
      ));
      
      // Insert search
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.browse-search',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 1,
      ));
      
      // Insert gutter menu
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.browse-menu-quick',
        'page_id' => $page_id,
        'parent_content_id' => $main_right_id,
        'order' => 2,
      ));
    }
  }
  
  protected function _addBlogListPage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'blog_index_list')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'blog_index_list',
        'displayname' => 'Blog List Page',
        'title' => 'Blog List',
        'description' => 'This page lists a member\'s blog entries.',
        'provides' => 'subject=user',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert left
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 1,
      ));
      $left_id = $db->lastInsertId();
      
      // Insert middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $middle_id = $db->lastInsertId();
      
      // Insert gutter
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.gutter-photo',
        'page_id' => $page_id,
        'parent_content_id' => $left_id,
        'order' => 1,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.gutter-menu',
        'page_id' => $page_id,
        'parent_content_id' => $left_id,
        'order' => 2,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.gutter-search',
        'page_id' => $page_id,
        'parent_content_id' => $left_id,
        'order' => 3,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 1,
      ));
      /*
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.comments',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 2,
      ));
      */
    }
  }
  
  protected function _addBlogViewPage()
  {
    $db = $this->getDb();

    // profile page
    $page_id = $db->select()
      ->from('engine4_core_pages', 'page_id')
      ->where('name = ?', 'blog_index_view')
      ->limit(1)
      ->query()
      ->fetchColumn();
    
    // insert if it doesn't exist yet
    if( !$page_id ) {
      // Insert page
      $db->insert('engine4_core_pages', array(
        'name' => 'blog_index_view',
        'displayname' => 'Blog View Page',
        'title' => 'Blog View',
        'description' => 'This page displays a blog entry.',
        'provides' => 'subject=blog',
        'custom' => 0,
      ));
      $page_id = $db->lastInsertId();
      
      // Insert main
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'main',
        'page_id' => $page_id,
      ));
      $main_id = $db->lastInsertId();
      
      // Insert left
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'left',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 1,
      ));
      $left_id = $db->lastInsertId();
      
      // Insert middle
      $db->insert('engine4_core_content', array(
        'type' => 'container',
        'name' => 'middle',
        'page_id' => $page_id,
        'parent_content_id' => $main_id,
        'order' => 2,
      ));
      $middle_id = $db->lastInsertId();
      
      // Insert gutter
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.gutter-photo',
        'page_id' => $page_id,
        'parent_content_id' => $left_id,
        'order' => 1,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.gutter-menu',
        'page_id' => $page_id,
        'parent_content_id' => $left_id,
        'order' => 2,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'blog.gutter-search',
        'page_id' => $page_id,
        'parent_content_id' => $left_id,
        'order' => 3,
      ));
      
      // Insert content
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.content',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 1,
      ));
      $db->insert('engine4_core_content', array(
        'type' => 'widget',
        'name' => 'core.comments',
        'page_id' => $page_id,
        'parent_content_id' => $middle_id,
        'order' => 2,
      ));
    }
  }
  
  protected function _addUserProfileContent()
  {
    //
    // install content areas
    //
    $db     = $this->getDb();
    $select = new Zend_Db_Select($db);

    // profile page
    $select
      ->from('engine4_core_pages')
      ->where('name = ?', 'user_profile_index')
      ->limit(1);
    $page_id = $select->query()->fetchObject()->page_id;


    // blog.profile-blogs

    // Check if it's already been placed
    $select = new Zend_Db_Select($db);
    $select
      ->from('engine4_core_content')
      ->where('page_id = ?', $page_id)
      ->where('type = ?', 'widget')
      ->where('name = ?', 'blog.profile-blogs')
      ;
    $info = $select->query()->fetch();

    if( empty($info) ) {

      // container_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('page_id = ?', $page_id)
        ->where('type = ?', 'container')
        ->limit(1);
      $container_id = $select->query()->fetchObject()->content_id;

      // middle_id (will always be there)
      $select = new Zend_Db_Select($db);
      $select
        ->from('engine4_core_content')
        ->where('parent_content_id = ?', $container_id)
        ->where('type = ?', 'container')
        ->where('name = ?', 'middle')
        ->limit(1);
      $middle_id = $select->query()->fetchObject()->content_id;

      // tab_id (tab container) may not always be there
      $select
        ->reset('where')
        ->where('type = ?', 'widget')
        ->where('name = ?', 'core.container-tabs')
        ->where('page_id = ?', $page_id)
        ->limit(1);
      $tab_id = $select->query()->fetchObject();
      if( $tab_id && @$tab_id->content_id ) {
          $tab_id = $tab_id->content_id;
      } else {
        $tab_id = null;
      }

      // tab on profile
      $db->insert('engine4_core_content', array(
        'page_id' => $page_id,
        'type'    => 'widget',
        'name'    => 'blog.profile-blogs',
        'parent_content_id' => ($tab_id ? $tab_id : $middle_id),
        'order'   => 6,
        'params'  => '{"title":"Blogs","titleCount":true}',
      ));

    }
  }
}
?>